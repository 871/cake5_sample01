<?php
declare(strict_types=1);

namespace App\Security\Auth;

use Cake\Http\ServerRequest;

final class AuthSession
{
    public const PREFIX = 'Auth';

    /**
     * @var string
     */
    private string $sessionKey;

    /**
     * @param \Cake\Http\ServerRequest $request
     */
    public function __construct(
        private readonly ServerRequest $request,
        string $type,
        string $account_id,
    ) {
        $this->sessionKey = join('.', [
            self::PREFIX,
            $type,
            $account_id,
        ]);
    }

    /**
     * @return array<string, string|null>
     */
    public function read(): array
    {
        /** @var array<string, string|null> */
        return $this->request->getSession()->read($this->sessionKey) ?? [];
    }

    /**
     * @return bool
     */
    public function check(): bool
    {
        return $this->request->getSession()->check($this->sessionKey);
    }

    /**
     * @param array<string, string|null> $data
     * @return void
     */
    public function write(array $data): void
    {
        $this->request->getSession()->write($this->sessionKey, $data);
    }

    /**
     * @return void
     */
    public function delete(): void
    {
        $this->request->getSession()->delete($this->sessionKey);
    }
}
