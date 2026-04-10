<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\Log\PageAccessLog;

use App\Domain\Log\PageAccessLogs\SearchCondition;
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
     * @return array<string, mixed>
     */
    public function getInitParams(): array
    {
        $datetime = DateTimeImmutable::createFromInterface($this->datetime);

        return [
            // 仕様: accessed_fromは現在日の23:59:59、accessed_toは前日の00:00:00
            'accessed_from' => $datetime->format('Y-m-d\T23:59:59'),
            'accessed_to' => $datetime->modify('-1 day')->format('Y-m-d\T00:00:00'),
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

                    return $from->modify('+ 7 days') >= $to;
                },
                'message' => __('アクセス日時の範囲は7日以内で指定してください。'),
            ]);
    }

    /**
     * @return self
     */
    public function search(): self
    {
        $condition = new SearchCondition(
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
                    $this->request->getQuery('navigation_type')
                ) ?? Vo\Search\NavigationType::FIRST,
            ),
            cursorAccessed: new Vo\Search\CursorAccessed(
                Cast::toStringOrNull(
                    $this->request->getQuery('cursor_accessed')
                ),
            ),
            cursorId: new Vo\Id(
                Cast::toStringOrNull(
                    $this->request->getQuery('cursor_id')
                ),
            ),
            limit: Cast::toIntOrNull(
                $this->request->getQuery('limit')
            ) ?? SearchCondition::DEFAULT_LIMIT,
        );

        $this->searchResults = (new PageAccessLogsRepository())->search($condition);

        return $this;
    }

    /**
     * 検索結果を返す（アクセス日時・プライマリIDの降順）
     *
     * @return array<\App\Domain\Log\PageAccessLogs\Entity\PageAccessLog>
     */
    public function getRows(): array
    {
        $navigationType = Cast::toStringOrNull(
            $this->request->getQuery('navigation_type')
        ) ?? Vo\Search\NavigationType::FIRST;
        $limit = Cast::toIntOrNull(
            $this->request->getQuery('limit')
        ) ?? SearchCondition::DEFAULT_LIMIT;

        return match ($navigationType) {
            Vo\Search\NavigationType::FIRST => array_slice($this->searchResults, 0, $limit),
            Vo\Search\NavigationType::LAST => array_slice($this->searchResults, 1),
            Vo\Search\NavigationType::NEXT => array_slice($this->searchResults, 0, $limit),
            Vo\Search\NavigationType::PREV => array_slice($this->searchResults, 1),
            default => throw new \Exception('Invalid navigation type: ' . $navigationType),
        };
    }

    /**
     * @return array<string, string>|null
     */
    public function getFirstCursor(): ?array
    {
        $navigationType = Cast::toStringOrNull(
            $this->request->getQuery('navigation_type')
        ) ?? Vo\Search\NavigationType::FIRST;
        $limit = Cast::toIntOrNull(
            $this->request->getQuery('limit')
        ) ?? SearchCondition::DEFAULT_LIMIT;

        return match ($navigationType) {
            Vo\Search\NavigationType::FIRST => null,
            Vo\Search\NavigationType::LAST => count($this->searchResults) > $limit ? [
                'navigation_type' => Vo\Search\NavigationType::FIRST,
                'cursor_id' => null,
                'cursor_accessed' => null,
            ] : null,
            Vo\Search\NavigationType::NEXT => [
                'navigation_type' => Vo\Search\NavigationType::FIRST,
                'cursor_id' => null,
                'cursor_accessed' => null,
            ],
            Vo\Search\NavigationType::PREV => count($this->searchResults) > $limit ? [
                'navigation_type' => Vo\Search\NavigationType::FIRST,
                'cursor_id' => null,
                'cursor_accessed' => null,
            ] : null,
            default => throw new \Exception('Invalid navigation type: ' . $navigationType),
        };
    }

    /**
     * @return array<string, string>|null
     */
    public function getLastCursor(): ?array
    {
        $navigationType = Cast::toStringOrNull(
            $this->request->getQuery('navigation_type')
        ) ?? Vo\Search\NavigationType::FIRST;
        $limit = Cast::toIntOrNull(
            $this->request->getQuery('limit')
        ) ?? SearchCondition::DEFAULT_LIMIT;

        return match ($navigationType) {
            Vo\Search\NavigationType::FIRST => count($this->searchResults) > $limit ? [
                'navigation_type' => Vo\Search\NavigationType::LAST,
                'cursor_id' => null,
                'cursor_accessed' => null,
            ] : null,
            Vo\Search\NavigationType::LAST => null,
            Vo\Search\NavigationType::NEXT => count($this->searchResults) > $limit ? [
                'navigation_type' => Vo\Search\NavigationType::LAST,
                'cursor_id' => null,
                'cursor_accessed' => null,
            ]: null,
            Vo\Search\NavigationType::PREV => [
                'navigation_type' => Vo\Search\NavigationType::LAST,
                'cursor_id' => null,
                'cursor_accessed' => null,
            ],
            default => throw new \Exception('Invalid navigation type: ' . $navigationType),
        };
    }

    /**
     * 前ページ（より新しいレコード）のカーソル情報を返す
     *
     * @return array<string, string>|null
     */
    public function getPrevCursor(): ?array
    {
        $navigationType = Cast::toStringOrNull(
            $this->request->getQuery('navigation_type')
        ) ?? Vo\Search\NavigationType::FIRST;
        $limit = Cast::toIntOrNull(
            $this->request->getQuery('limit')
        ) ?? SearchCondition::DEFAULT_LIMIT;

        return match ($navigationType) {
            Vo\Search\NavigationType::FIRST => null,
            Vo\Search\NavigationType::LAST => count($this->searchResults) > $limit ? [
                'navigation_type' => Vo\Search\NavigationType::PREV,
                'cursor_id' => $this->searchResults[1]->id()->toString(),
                'cursor_accessed' => $this->searchResults[1]->accessed()->format('Y-m-d\TH:i:s.u'),
            ] : null,
            Vo\Search\NavigationType::NEXT => [
                'navigation_type' => Vo\Search\NavigationType::PREV,
                'cursor_id' => $this->searchResults[0]->id()->toString(),
                'cursor_accessed' => $this->searchResults[0]->accessed()->format('Y-m-d\TH:i:s.u'),
            ],
            Vo\Search\NavigationType::PREV => count($this->searchResults) > $limit ? [
                'navigation_type' => Vo\Search\NavigationType::PREV,
                'cursor_id' => $this->searchResults[1]->id()->toString(),
                'cursor_accessed' => $this->searchResults[1]->accessed()->format('Y-m-d\TH:i:s.u'),
            ] : null,
            default => throw new \Exception('Invalid navigation type: ' . $navigationType),
        };
    }

    /**
     * 次ページ（より古いレコード）のカーソル情報を返す
     *
     * @return array<string, string>|null
     */
    public function getNextCursor(): ?array
    {
        $navigationType = Cast::toStringOrNull(
            $this->request->getQuery('navigation_type')
        ) ?? Vo\Search\NavigationType::FIRST;
        $limit = Cast::toIntOrNull(
            $this->request->getQuery('limit')
        ) ?? SearchCondition::DEFAULT_LIMIT;

        return match ($navigationType) {
            Vo\Search\NavigationType::FIRST => count($this->searchResults) > $limit ? [
                'navigation_type' => Vo\Search\NavigationType::NEXT,
                'cursor_id' => $this->searchResults[$limit - 1]->id()->toString(),
                'cursor_accessed' => $this->searchResults[$limit - 1]->accessed()->format('Y-m-d\TH:i:s.u'),
            ] : null,
            Vo\Search\NavigationType::LAST => null,
            Vo\Search\NavigationType::NEXT => count($this->searchResults) > $limit ? [
                'navigation_type' => Vo\Search\NavigationType::NEXT,
                'cursor_id' => $this->searchResults[$limit - 1]->id()->toString(),
                'cursor_accessed' => $this->searchResults[$limit - 1]->accessed()->format('Y-m-d\TH:i:s.u'),
            ] : null,
            Vo\Search\NavigationType::PREV => [
                'navigation_type' => Vo\Search\NavigationType::NEXT,
                'cursor_id' => $this->searchResults[count($this->searchResults) - 1]->id()->toString(),
                'cursor_accessed' => 
                    $this->searchResults[count($this->searchResults) - 1]->accessed()->format('Y-m-d\TH:i:s.u'),
            ],
            default => throw new \Exception('Invalid navigation type: ' . $navigationType),
        };
    }
}
