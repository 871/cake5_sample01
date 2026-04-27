<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\Log\PageAccessLog;

use App\Domain\Log\PageAccessLogs\SearchCondition;
use App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogsRepository\SearchResults;
use App\Domain\Log\PageAccessLogs\ValueObject as Vo;
use App\Exception\ValidateException;
use App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogsRepository;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use Cake\Validation\Validator;
use DateTimeImmutable;

final class Search implements ServiceInterface
{
    use ServiceTrait;
    
    /**
     * @return array<\App\Domain\Log\PageAccessLogs\Entity\PageAccessLog>
     */
    private array $searchResults = [];

    /**
     * @var bool 前ページ存在フラグ
     */
    private bool $isPrevExists = false;

    /**
     * @var bool 次ページ存在フラグ
     */
    private bool $isNextExists = false;

    /**
     * @return array<string, mixed>
     */
    public function getInitParams(): array
    {
        $datetime = DateTimeImmutable::createFromInterface($this->datetime);

        return [
            // 仕様: accessed_fromは現在日の23:59:59、accessed_toは13日前の00:00:00
            'accessed_from' => $datetime->modify('-1 day')->format('Y-m-d\T00:00:00'),
            'accessed_to' => $datetime->modify('-1 day')->format('Y-m-d\T23:59:59'),
        ];
    }

    /**
     * バリデーション
     *
     * @return self
     */
    public function validate(): self
    {
        $error = $this->getValidator()
            ->validate($this->request->getQuery());

        if ($error !== []) {
            throw new ValidateException($error);
        }

        return $this;
    }

    /**
     * @return \Cake\Validation\Validator
     */
    private function getValidator(): Validator
    {
         return (new Validator())
            ->notEmptyDateTime('accessed_from', __('アクセス日時（自）を入力してください。'))
            ->add('accessed_from', 'validFormat', [
                'rule' => function ($value) {
                    return Cast::toDateTimeOrNull($value, 'Y-m-d\TH:i:s') !== null;
                },
                'message' => __('アクセス日時（自）は正しい日時形式で入力してください。'),
            ])
            ->notEmptyDateTime('accessed_to', __('アクセス日時（至）を入力してください。'))
            ->add('accessed_to', 'validFormat', [
                'rule' => function ($value) {
                    return Cast::toDateTimeOrNull($value, 'Y-m-d\TH:i:s') !== null;
                },
                'message' => __('アクセス日時（至）は正しい日時形式で入力してください。'),
            ])
            ->add('accessed_to', 'withinSevenDays', [
                'rule' => function ($value, $context) {
                    /** @var DateTimeImmutable|null $from */
                    $from = Cast::toDateTimeOrNull($context['data']['accessed_from'] ?? null);
                    /** @var DateTimeImmutable|null $to */
                    $to = Cast::toDateTimeOrNull($value);
                    if ($from === null || $to === null) {
                        return true; // フォーマットエラーは別のルールでキャッチするためここではスルー
                    }

                    return $from->modify('+ 14 days') >= $to;
                },
                'message' => __('アクセス日時の範囲は14日以内で指定してください。'),
            ]);
    }

    /**
     * @return self
     */
    public function search(): self
    {
        $this->searchResults = (new PageAccessLogsRepository())->search($this->createSearchCondition());

        $this->isPrevExists = (function(): bool {
            $searchKey = $this->searchResults[0]?->searchKey()->toString() ?? '';
            if ($searchKey === '') {
                return false;
            }
            return (new PageAccessLogsRepository())->search($this->createSearchCondition(
                navigation_type: Vo\Search\NavigationType::PREV,
                search_key: $searchKey,
                limit: 1,
            )) !== [];
        })();
        $this->isNextExists = (function(): bool {
            $searchKey = $this->searchResults[count($this->searchResults) - 1]?->searchKey()->toString() ?? '';
            if ($searchKey === '') {
                return false;
            }
            return (new PageAccessLogsRepository())->search($this->createSearchCondition(
                navigation_type: Vo\Search\NavigationType::NEXT,
                search_key: $searchKey,
                limit: 1,
            )) !== [];
        })();

        return $this;
    }

    /**
     * @param string|null $navigation_type ナビゲーションタイプ（FIRST, LAST, NEXT, PREV）
     * @param string|null $search_key カーソルベースページネーションの検索キー
     * @param int|null $limit 1ページあたりの件数
     * @return \App\Domain\Log\PageAccessLogs\SearchCondition
     */
    private function createSearchCondition(
        ?string $navigation_type = null,
        ?string $search_key = null,
        ?int $limit = null,
    ): SearchCondition {
        return new SearchCondition(
            accessedFrom: new Vo\Accessed(
                StrictCast::toDateTimeString(
                    $this->request->getQuery('accessed_from'),
                    'Y-m-d\TH:i:s',
                ),
            ),
            accessedTo: new Vo\Accessed(
                StrictCast::toDateTimeString(
                    $this->request->getQuery('accessed_to'),
                    'Y-m-d\TH:i:s',
                ),
            ),
            accountType: new Vo\Search\AccountType(
                Cast::toStringOrNull(
                    $this->request->getQuery('account_type'),
                ),
            ),
            accountId: new Vo\Search\AccountId(
                Cast::toStringOrNull(
                    $this->request->getQuery('account_id'),
                ),
            ),
            keyword: new Vo\Search\Keyword(
                Cast::toStringOrNull(
                    $this->request->getQuery('keyword'),
                ),
            ),
            navigationType: new Vo\Search\NavigationType(
                Cast::toStringOrNull(
                    $navigation_type ?? $this->request->getQuery('navigation_type')
                ) ?? Vo\Search\NavigationType::FIRST,
            ),
            searchKey: new Vo\SearchKey(
                Cast::toStringOrNull(
                    $search_key ?? $this->request->getQuery('search_key')
                ),
            ),
            limit: Cast::toIntOrNull(
                $limit ?? $this->request->getQuery('limit')
            ) ?? SearchCondition::DEFAULT_LIMIT,
        );
    }

    /**
     * 検索結果を返す（アクセス日時・プライマリIDの降順）
     *
     * @return array<\App\Domain\Log\PageAccessLogs\Entity\PageAccessLog>
     */
    public function getRows(): array
    {
        return $this->searchResults;
    }

    /**
     * 前ページ存在フラグを返す
     *
     * @return bool
     */
    public function isPrevExists(): bool
    {
        return $this->isPrevExists;
    }

    /**
     * 次ページ存在フラグを返す
     *
     * @return bool
     */
    public function isNextExists(): bool
    {
        return $this->isNextExists;
    }
}
