<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin\MailManage;

use App\Domain\Mail\SearchCondition;
use App\Domain\Mail\ValueObject as Vo;
use App\Exception\ValidateException;
use App\Infrastructure\Persistence\Cake\Mail\MailsRepository;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use Cake\Validation\Validator;
use DateTimeImmutable;

final class Search implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @var array<\App\Domain\Mail\Entity\Mail>
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
            'send_scheduled_at_from' => $datetime->modify('-1 day')->format('Y-m-d\T00:00:00'),
            'send_scheduled_at_to' => $datetime->format('Y-m-d\T23:59:59'),
        ];
    }

    /**
     * バリデーション
     *
     * @return self
     */
    public function validate(): self
    {
        /** @var array<string, mixed> $data */
        $data = $this->request->getQuery();
        $error = $this->getValidator()
            ->validate($data);

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
            ->notEmptyDateTime('send_scheduled_at_from', __('送信予定日時（自）を入力してください。'))
            ->add('send_scheduled_at_from', 'validFormat', [
                'rule' => function ($value) {
                    return Cast::toDateTimeOrNull($value) !== null;
                },
                'message' => __('送信予定日時（自）は正しい日時形式で入力してください。'),
            ])
            ->notEmptyDateTime('send_scheduled_at_to', __('送信予定日時（至）を入力してください。'))
            ->add('send_scheduled_at_to', 'validFormat', [
                'rule' => function ($value) {
                    return Cast::toDateTimeOrNull($value) !== null;
                },
                'message' => __('送信予定日時（至）は正しい日時形式で入力してください。'),
            ])
            ->add('send_scheduled_at_to', 'withinFourteenDays', [
                'rule' => function ($value, array $context) {
                    /** @var array<string, mixed> $data */
                    $data = $context['data'] ?? [];
                    /** @var \DateTimeImmutable|null $from */
                    $from = Cast::toDateTimeOrNull($data['send_scheduled_at_from'] ?? null);
                    /** @var \DateTimeImmutable|null $to */
                    $to = Cast::toDateTimeOrNull($value);
                    if ($from === null || $to === null) {
                        return true; // フォーマットエラーは別のルールでキャッチするためここではスルー
                    }

                    return $from->modify('+ 14 days') >= $to;
                },
                'message' => __('送信予定日時の範囲は14日以内で指定してください。'),
            ]);
    }

    /**
     * @return self
     */
    public function search(): self
    {
        $this->searchResults = (new MailsRepository())->search($this->createSearchCondition());

        $this->isPrevExists = (function (): bool {
            if (count($this->searchResults) === 0) {
                return false;
            }

            $searchKey = $this->searchResults[0]->id()->toStringOrNull() ?? '';
            if ($searchKey === '') {
                return false;
            }

            return (new MailsRepository())->search($this->createSearchCondition(
                navigation_type: Vo\Search\NavigationType::PREV,
                search_key: $searchKey,
                limit: 1,
            )) !== [];
        })();
        $this->isNextExists = (function (): bool {
            if (count($this->searchResults) === 0) {
                return false;
            }

            $searchKey = $this->searchResults[count($this->searchResults) - 1]->id()->toStringOrNull() ?? '';
            if ($searchKey === '') {
                return false;
            }

            return (new MailsRepository())->search($this->createSearchCondition(
                navigation_type: Vo\Search\NavigationType::NEXT,
                search_key: $searchKey,
                limit: 1,
            )) !== [];
        })();

        return $this;
    }

    /**
     * @param string|null $navigation_type ナビゲーションタイプ（FIRST, LAST, NEXT, PREV）
     * @param string|null $search_key カーソルベースページネーションの検索キー（プライマリID）
     * @param int|null $limit 1ページあたりの件数
     * @return \App\Domain\Mail\SearchCondition
     */
    private function createSearchCondition(
        ?string $navigation_type = null,
        ?string $search_key = null,
        ?int $limit = null,
    ): SearchCondition {
        return new SearchCondition(
            sendScheduledAtFrom: new Vo\SendScheduledAt(
                StrictCast::toDateTimeString(
                    $this->request->getQuery('send_scheduled_at_from'),
                    'Y-m-d\TH:i:s',
                ),
                'Y-m-d\TH:i:s',
            ),
            sendScheduledAtTo: new Vo\SendScheduledAt(
                StrictCast::toDateTimeString(
                    $this->request->getQuery('send_scheduled_at_to'),
                    'Y-m-d\TH:i:s',
                ),
                'Y-m-d\TH:i:s',
            ),
            sendStatus: ($status = Cast::toStringOrNull($this->request->getQuery('send_status'))) !== null
                ? new Vo\SendStatus($status)
                : null,
            relatedDataKey: new Vo\RelatedDataKey(
                Cast::toStringOrNull($this->request->getQuery('related_data_key')),
            ),
            navigationType: new Vo\Search\NavigationType(
                Cast::toStringOrNull(
                    $navigation_type ?? $this->request->getQuery('navigation_type'),
                ) ?? Vo\Search\NavigationType::FIRST,
            ),
            searchKey: new Vo\Search\SearchKey(
                Cast::toStringOrNull(
                    $search_key ?? $this->request->getQuery('search_key'),
                ),
            ),
            limit: Cast::toIntOrNull(
                $limit ?? $this->request->getQuery('limit'),
            ) ?? SearchCondition::DEFAULT_LIMIT,
            keyword: ($kw = Cast::toStringOrNull($this->request->getQuery('keyword'))) !== null
                ? new Vo\Search\Keyword($kw)
                : null,
        );
    }

    /**
     * 検索結果を返す（プライマリIDの昇順）
     *
     * @return array<\App\Domain\Mail\Entity\Mail>
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
