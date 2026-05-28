<?php
declare(strict_types=1);

namespace App\Test\TestCase\Security\Auth;

use App\Model\Entity\User\UserAccount;
use App\Security\Auth\UserTokenService;
use Cake\ORM\Entity;
use Cake\TestSuite\TestCase;
use DateTimeImmutable;

final class UserTokenServiceTest extends TestCase
{
    public function testCreateTokenSetAndReadTokens(): void
    {
        $service = new UserTokenService();
        $now = new DateTimeImmutable('2026-05-17 12:00:00');
        $tokenSet = $service->createTokenSet($this->makeAccount(), $now);

        $this->assertSame('100001', $tokenSet['auth']['account_id']);
        $this->assertSame('user', $tokenSet['auth']['type']);

        $access = $service->readAccessToken($tokenSet['access_token']);
        $this->assertNotNull($access);
        $this->assertSame('100001', $access['account_id']);

        $refresh = $service->readRefreshToken($tokenSet['refresh_token']);
        $this->assertNotNull($refresh);
        $this->assertSame('100001', $refresh['account_id']);
        $this->assertSame($tokenSet['refresh_token_id'], $refresh['refresh_token_id']);

        $this->assertCount(2, $tokenSet['set_cookie_headers']);
    }

    public function testReadAccessTokenReturnsNullWhenExpired(): void
    {
        $service = new UserTokenService();
        $tokenSet = $service->createTokenSet(
            $this->makeAccount(),
            new DateTimeImmutable('-1 day'),
        );

        $this->assertNull($service->readAccessToken($tokenSet['access_token']));
        $refresh = $service->readRefreshToken($tokenSet['refresh_token']);
        $this->assertNotNull($refresh);
        $this->assertSame('100001', $refresh['account_id']);
    }

    private function makeAccount(): UserAccount
    {
        $account = new UserAccount([
            'id' => 100001,
            'email' => 'user@example.com',
            'password' => 'hashed-password',
            'name' => 'Sample User',
            'account_status_master_id' => 200,
            'is_email_verified' => 1,
            'password_changed_at' => new DateTimeImmutable('2026-01-01 00:00:00'),
            'password_expires_at' => new DateTimeImmutable('2027-01-01 00:00:00'),
            'created' => new DateTimeImmutable('2026-01-01 00:00:00'),
            'modified' => new DateTimeImmutable('2026-01-01 00:00:00'),
        ]);
        $account->set('account_status_master', new Entity([
            'code' => 'ACTIVE',
            'name' => '有効',
        ]));

        return $account;
    }
}
