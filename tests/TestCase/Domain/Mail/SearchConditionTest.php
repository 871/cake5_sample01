<?php
declare(strict_types=1);

namespace App\Test\TestCase\Domain\Mail;

use App\Domain\Mail\SearchCondition;
use App\Domain\Mail\ValueObject\RelatedDataKey;
use App\Domain\Mail\ValueObject\Search\Keyword;
use App\Domain\Mail\ValueObject\Search\NavigationType;
use App\Domain\Mail\ValueObject\Search\SearchKey;
use App\Domain\Mail\ValueObject\SendScheduledAt;
use Cake\TestSuite\TestCase;

final class SearchConditionTest extends TestCase
{
    public function testGetKeywordWhenSpecified(): void
    {
        $condition = new SearchCondition(
            sendScheduledAtFrom: new SendScheduledAt('2026-01-01T00:00:00'),
            sendScheduledAtTo: new SendScheduledAt('2026-01-31T23:59:59'),
            sendStatus: null,
            relatedDataKey: new RelatedDataKey(null),
            navigationType: new NavigationType(NavigationType::FIRST),
            searchKey: new SearchKey(null),
            keyword: new Keyword('mail body'),
        );

        $this->assertSame('mail body', $condition->getKeyword()?->toString());
    }

    public function testGetKeywordWhenNotSpecified(): void
    {
        $condition = new SearchCondition(
            sendScheduledAtFrom: new SendScheduledAt('2026-01-01T00:00:00'),
            sendScheduledAtTo: new SendScheduledAt('2026-01-31T23:59:59'),
            sendStatus: null,
            relatedDataKey: new RelatedDataKey(null),
            navigationType: new NavigationType(NavigationType::FIRST),
            searchKey: new SearchKey(null),
        );

        $this->assertNull($condition->getKeyword());
    }
}
