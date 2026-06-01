<?php
declare(strict_types=1);

namespace App\Security\Auth;

use App\Domain\User\UserAccounts\Entity\UserAccount;
use App\Lib\UUID\UUID;
use App\Security\Auth\AuthContext\Fields\Type;
use App\Security\Input\StrictCast;
use Cake\Utility\Security;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Psr\Http\Message\ResponseInterface;

final class UserTokenService
{
    public const ACCESS_TOKEN_COOKIE = 'user_access_token';
    public const REFRESH_TOKEN_COOKIE = 'user_refresh_token';
    public const REQUEST_ATTRIBUTE = 'userAuth';
    public const COOKIE_PATH = '/v1/us';
    private const ACCESS_TOKEN_TTL = 'PT15M';
    private const REFRESH_TOKEN_TTL = 'P30D';

    /**
     * @param \App\Domain\User\UserAccounts\Entity\UserAccount $account
     * @param \DateTimeInterface $now
     * @return array{auth: array<string, string>, access_token: string, refresh_token: string, refresh_token_id: string, refresh_token_expires_at: \DateTimeImmutable, set_cookie_headers: array<int, string>}
     */
    public function createTokenSet(UserAccount $account, DateTimeInterface $now): array
    {
        $issuedAt = DateTimeImmutable::createFromInterface($now);
        $auth = $this->buildAuthPayload($account, $issuedAt);
        $accessExpiresAt = $issuedAt->add(new DateInterval(self::ACCESS_TOKEN_TTL));
        $refreshExpiresAt = $issuedAt->add(new DateInterval(self::REFRESH_TOKEN_TTL));
        $refreshTokenId = UUID::uuid7();

        $accessToken = $this->encode(array_merge($auth, [
            'token_type' => 'access',
            'iat' => (string)$issuedAt->getTimestamp(),
            'exp' => (string)$accessExpiresAt->getTimestamp(),
        ]));
        $refreshToken = $this->encode([
            'account_id' => $auth['account_id'],
            'refresh_token_id' => $refreshTokenId,
            'token_type' => 'refresh',
            'iat' => (string)$issuedAt->getTimestamp(),
            'exp' => (string)$refreshExpiresAt->getTimestamp(),
        ]);

        return [
            'auth' => $auth,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'refresh_token_id' => $refreshTokenId,
            'refresh_token_expires_at' => $refreshExpiresAt,
            'set_cookie_headers' => [
                $this->buildCookieHeader(self::ACCESS_TOKEN_COOKIE, $accessToken, $accessExpiresAt),
                $this->buildCookieHeader(self::REFRESH_TOKEN_COOKIE, $refreshToken, $refreshExpiresAt),
            ],
        ];
    }

    /**
     * @return array{
     *     account_id: string,
     *     type: string
     * }|null
     */
    public function readAccessToken(?string $token): ?array
    {
        $claims = $this->readToken($token, 'access');
        if ($claims === null) {
            return null;
        }

        unset($claims['token_type'], $claims['iat'], $claims['exp']);

        /** @var array{ account_id: string, type: string } */
        return $claims;
    }

    /**
     * @param ?string $token
     * @return ?array<string, string>
     */
    public function readRefreshToken(?string $token): ?array
    {
        return $this->readToken($token, 'refresh');
    }

    /**
     * @return array<int, string>
     */
    public function createExpiredCookieHeaders(): array
    {
        return [
            $this->buildExpiredCookieHeader(self::ACCESS_TOKEN_COOKIE),
            $this->buildExpiredCookieHeader(self::REFRESH_TOKEN_COOKIE),
        ];
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array<int, string> $setCookieHeaders
     * @return \Cake\Http\Response
     */
    public function withCookieHeaders(
        ResponseInterface $response,
        array $setCookieHeaders,
    ): ResponseInterface {
        foreach ($setCookieHeaders as $header) {
            $response = $response->withAddedHeader('Set-Cookie', $header);
        }

        /** @var \Cake\Http\Response $response */
        return $response;
    }

    /**
     * @param \App\Domain\User\UserAccounts\Entity\UserAccount $account
     * @param \DateTimeInterface $now
     * @return array<string, string>
     */
    private function buildAuthPayload(UserAccount $account, DateTimeInterface $now): array
    {
        return [
            'type' => Type::TYPE_USER,
            'account_id' => (string)$account->id()->toString(),
            'account_email' => (string)$account->email()->toString(),
            'account_name' => (string)$account->name()->toString(),
            'account_status_master_id' => (string)$account->accountStatusMasterId()->toString(),
            'account_status_master_code' => (string)$account->accountStatusMasterCode()->toString(),
            'account_status_master_name' => (string)$account->accountStatusMasterName()->toString(),
            'is_email_verified' => (string)$account->isEmailVerified()->toString(),
            'password_changed_at' => (string)$account->passwordChangedAt()->format('Y-m-d\TH:i:s'),
            'password_expires_at' => (string)$account->passwordExpiresAt()->format('Y-m-d\TH:i:s'),
            'created' => (string)$account->created()->format('Y-m-d\TH:i:s'),
            'modified' => (string)$account->modified()->format('Y-m-d\TH:i:s'),
            'logined' => (string)$now->format('Y-m-d\TH:i:s'),
        ];
    }

    /**
     * @param ?string $token
     * @param string $expectedTokenType
     * @return ?array<string, string>
     */
    private function readToken(?string $token, string $expectedTokenType): ?array
    {
        if ($token === null || $token === '') {
            return null;
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;
        $expectedSignature = $this->base64UrlEncode(hash_hmac(
            'sha256',
            $encodedHeader . '.' . $encodedPayload,
            Security::getSalt(),
            true,
        ));
        if (!hash_equals($expectedSignature, $encodedSignature)) {
            return null;
        }

        $payloadJson = $this->base64UrlDecode($encodedPayload);
        if ($payloadJson === null) {
            return null;
        }

        $claims = json_decode($payloadJson, true);
        if (!is_array($claims)) {
            return null;
        }

        $tokenType = $claims['token_type'] ?? null;
        $expiresAt = $claims['exp'] ?? null;
        if (
            !is_string($tokenType)
            || $tokenType !== $expectedTokenType
            || !is_string($expiresAt)
            || !ctype_digit($expiresAt)
        ) {
            return null;
        }

        if ((int)$expiresAt < (new DateTimeImmutable())->getTimestamp()) {
            return null;
        }

        /** @var array<string, string> */
        return array_map(
            static fn(mixed $value): string => StrictCast::toString($value),
            $claims,
        );
    }

    /**
     * @param array<string, string> $claims
     * @return string
     */
    private function encode(array $claims): string
    {
        $encodedHeader = $this->base64UrlEncode((string)json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT',
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        $encodedPayload = $this->base64UrlEncode((string)json_encode(
            $claims,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
        ));
        $signature = $this->base64UrlEncode(hash_hmac(
            'sha256',
            $encodedHeader . '.' . $encodedPayload,
            Security::getSalt(),
            true,
        ));

        return $encodedHeader . '.' . $encodedPayload . '.' . $signature;
    }

    /**
     * @param string $name
     * @param string $value
     * @param \DateTimeInterface $expiresAt
     * @return string
     */
    private function buildCookieHeader(string $name, string $value, DateTimeInterface $expiresAt): string
    {
        $maxAge = max(0, $expiresAt->getTimestamp() - (new DateTimeImmutable())->getTimestamp());

        return sprintf(
            '%s=%s; Expires=%s; Max-Age=%d; Path=%s; HttpOnly; SameSite=Lax',
            $name,
            rawurlencode($value),
            gmdate('D, d M Y H:i:s', $expiresAt->getTimestamp()) . ' GMT',
            $maxAge,
            self::COOKIE_PATH,
        );
    }

    /**
     * @param string $name
     * @return string
     */
    private function buildExpiredCookieHeader(string $name): string
    {
        return sprintf(
            '%s=deleted; Expires=Thu, 01 Jan 1970 00:00:00 GMT; Max-Age=0; Path=%s; HttpOnly; SameSite=Lax',
            $name,
            self::COOKIE_PATH,
        );
    }

    /**
     * @param string $value
     * @return string
     */
    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    /**
     * @param string $value
     * @return ?string
     */
    private function base64UrlDecode(string $value): ?string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/') . str_repeat('=', (4 - strlen($value) % 4) % 4), true);

        return $decoded === false ? null : $decoded;
    }
}
