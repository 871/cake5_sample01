<?php
declare(strict_types=1);

namespace App\Test\TestCase\Security\Auth;

use App\Domain\User\UserAccounts\Entity\UserAccount;
use App\Domain\User\UserAccounts\ValueObject as UserAccountVo;
use App\Domain\Shared\ValueObject as SVo;
use App\Security\Auth\UserTokenService;
use Cake\TestSuite\TestCase;
use DateTimeImmutable;

final class UserTokenServiceTest extends TestCase
{
    public function testCreateTokenSetAndReadTokens(): void
    {
        $service = new UserTokenService();
        $now = new DateTimeImmutable();
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
        return new UserAccount(
            id: new UserAccountVo\Id('100001'),
            email: UserAccountVo\Email::fromString('user@example.com'),
            password: UserAccountVo\Password::fromString('hashed-password'),
            name: UserAccountVo\Name::fromString('Sample User'),
            account_status_master_id: new UserAccountVo\AccountStatusMasterId('200'),
            account_status_master_code: new UserAccountVo\AccountStatusMasterCode('ACTIVE'),
            account_status_master_name: new UserAccountVo\AccountStatusMasterName('有効'),
            is_email_verified: new UserAccountVo\IsEmailVerified('1'),
            password_changed_at: new UserAccountVo\PasswordChangedAt('2026-01-01T00:00:00'),
            password_expires_at: new UserAccountVo\PasswordExpiresAt('2027-01-01T00:00:00'),
            created: new SVo\Created('2026-01-01T00:00:00'),
            created_by: new SVo\CreatedBy(null),
            created_ip: new SVo\CreatedIp(null),
            modified: new SVo\Modified('2026-01-01T00:00:00'),
            modified_by: new SVo\ModifiedBy(null),
            modified_ip: new SVo\ModifiedIp(null),
        );
    }
}
