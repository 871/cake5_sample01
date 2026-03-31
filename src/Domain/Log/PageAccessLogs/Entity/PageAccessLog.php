<?php
declare(strict_types=1);

namespace App\Domain\Log\PageAccessLogs\Entity;

use App\Domain\Log\PageAccessLogs\ValueObject as Vo;
use App\Domain\Shared\ValueObject as SVo;

final class PageAccessLog
{
    /**
     * @param ?string $id
     * @param ?string $accessed
     * @param ?string $account_type
     * @param ?string $account_id
     * @param ?string $method
     * @param ?string $path
     * @param ?string $query_string
     * @param ?string $post_keys
     * @param ?string $route_name
     * @param ?string $referer
     * @param ?string $ip_address
     * @param ?string $user_agent
     * @param ?string $created
     */
    public function __construct(
        private readonly ?string $id,
        private readonly ?string $accessed,
        private readonly ?string $account_type,
        private readonly ?string $account_id,
        private readonly ?string $method,
        private readonly ?string $path,
        private readonly ?string $query_string,
        private readonly ?string $post_keys,
        private readonly ?string $route_name,
        private readonly ?string $referer,
        private readonly ?string $ip_address,
        private readonly ?string $user_agent,
        private readonly ?string $created,
    ) {
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\Id
     */
    public function id(): Vo\Id
    {
        return Vo\Id::fromString($this->id);
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\Accessed
     */
    public function accessed(): Vo\Accessed
    {
        return new Vo\Accessed($this->accessed);
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\AccountType
     */
    public function accountType(): Vo\AccountType
    {
        return Vo\AccountType::fromString($this->account_type);
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\AccountId
     */
    public function accountId(): Vo\AccountId
    {
        return new Vo\AccountId($this->account_id);
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\Method
     */
    public function method(): Vo\Method
    {
        return Vo\Method::fromString($this->method);
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\Path
     */
    public function path(): Vo\Path
    {
        return Vo\Path::fromString($this->path);
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\QueryString
     */
    public function queryString(): Vo\QueryString
    {
        return Vo\QueryString::fromString($this->query_string);
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\PostKeys
     */
    public function postKeys(): Vo\PostKeys
    {
        return Vo\PostKeys::fromString($this->post_keys);
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\RouteName
     */
    public function routeName(): Vo\RouteName
    {
        return Vo\RouteName::fromString($this->route_name);
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\Referer
     */
    public function referer(): Vo\Referer
    {
        return Vo\Referer::fromString($this->referer);
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\IpAddress
     */
    public function ipAddress(): Vo\IpAddress
    {
        return Vo\IpAddress::fromString($this->ip_address);
    }

    /**
     * @return \App\Domain\Log\PageAccessLogs\ValueObject\UserAgent
     */
    public function userAgent(): Vo\UserAgent
    {
        return Vo\UserAgent::fromString($this->user_agent);
    }

    /**
     * @return \App\Domain\Shared\ValueObject\Created
     */
    public function created(): SVo\Created
    {
        return new SVo\Created($this->created);
    }
}
