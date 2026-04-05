<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\Log\PageAccessLog;

use App\Domain\Log\PageAccessLogs\SearchCondition;
use App\Domain\Log\PageAccessLogs\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Log\PageAccessLogs\PageAccessLogsRepository;
use App\Security\Input\Cast;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;
use DateTimeImmutable;

final class Search implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return array<string, mixed>
     */
    public function getInitParams(): array
    {
        $datetime = DateTimeImmutable::createFromInterface($this->datetime);

        return [
            'accessed_from' => $datetime->format('Y-m-d\T23:59:59'),
            'accessed_to' => $datetime->modify('-1 day')->format('Y-m-d\T00:00:00'),
        ];
    }

    /**
     * バリデーション
     *
     * @return array<string>
     */
    public function validate(): array
    {
        $errors = [];

        /** @var array<string, string> $data */
        $data = $this->request->getQuery();

        $accessedFromRaw = Cast::toDateTimeStringOrNull($data['accessed_from'] ?? null, 'Y-m-d\TH:i:s');
        $accessedToRaw = Cast::toDateTimeStringOrNull($data['accessed_to'] ?? null, 'Y-m-d\TH:i:s');

        if ($accessedFromRaw === null) {
            $errors[] = 'アクセス日時（自）は必須です。';
        }

        if ($accessedToRaw === null) {
            $errors[] = 'アクセス日時（至）は必須です。';
        }

        if ($accessedFromRaw !== null && $accessedToRaw !== null) {
            $from = Cast::toDateTimeOrNull($accessedFromRaw);
            $to = Cast::toDateTimeOrNull($accessedToRaw);

            if ($from !== null && $to !== null) {
                $diffSeconds = abs($to->getTimestamp() - $from->getTimestamp());
                $sevenDaysInSeconds = 7 * 24 * 60 * 60;

                if ($diffSeconds > $sevenDaysInSeconds) {
                    $errors[] = 'アクセス日時の範囲は7日以内で指定してください。';
                }
            }
        }

        return $errors;
    }

    /**
     * 検索結果を返す（アクセス日時・プライマリIDの降順）
     *
     * @return array<\App\Domain\Log\PageAccessLogs\Entity\PageAccessLog>
     */
    public function getRows(): array
    {
        /** @var array<string, string> $data */
        $data = $this->request->getQuery();

        $navigationType = Cast::toStringOrNull($data['navigation_type'] ?? null);
        $cursorId = Cast::toStringOrNull($data['cursor_id'] ?? null);
        $cursorAccessed = Cast::toDateTimeStringOrNull($data['cursor_accessed'] ?? null, 'Y-m-d\TH:i:s');

        // NEXT/PREV指定がなければLAST（最新レコード取得）
        if ($navigationType === null || !in_array($navigationType, Vo\NavigationType::VALUES, true)) {
            $navigationType = Vo\NavigationType::LAST;
            $cursorId = null;
            $cursorAccessed = null;
        }

        [$accessedFrom, $accessedTo] = $this->resolveAccessedRange();

        $condition = new SearchCondition(
            accessedFrom: new Vo\Accessed($accessedFrom),
            accessedTo: new Vo\Accessed($accessedTo),
            accountType: new Vo\AccountType(Cast::toStringOrNull($data['account_type'] ?? null)),
            accountId: new Vo\AccountId(Cast::toStringOrNull($data['account_id'] ?? null)),
            keyword: new Vo\Search\Keyword(Cast::toStringOrNull($data['keyword'] ?? null)),
            navigationType: new Vo\NavigationType($navigationType),
            cursorId: new Vo\Id($cursorId),
            cursorAccessed: new Vo\Accessed($cursorAccessed),
        );

        $rows = (new PageAccessLogsRepository())->search($condition);

        // 降順（最新順）で表示するため逆順にする
        return array_reverse($rows);
    }

    /**
     * 前ページ（より新しいレコード）のカーソル情報を返す
     *
     * @param array<\App\Domain\Log\PageAccessLogs\Entity\PageAccessLog> $rows
     * @return array<string, string>|null
     */
    public function getPrevCursor(array $rows): ?array
    {
        if (empty($rows)) {
            return null;
        }

        /** @var array<string, string> $data */
        $data = $this->request->getQuery();
        $navigationType = Cast::toStringOrNull($data['navigation_type'] ?? null);

        // 初期ページ（LAST）の場合は前ページなし
        if ($navigationType === null || $navigationType === Vo\NavigationType::LAST) {
            return null;
        }

        // 降順表示の先頭（最新レコード）がカーソル
        $newest = $rows[0];
        $newestAccessed = $newest->accessed()->format('Y-m-d\TH:i:s');
        $newestId = $newest->id()->toStringOrNull();

        if ($newestAccessed === null || $newestId === null) {
            return null;
        }

        return [
            'navigation_type' => Vo\NavigationType::NEXT,
            'cursor_id' => $newestId,
            'cursor_accessed' => $newestAccessed,
        ];
    }

    /**
     * 次ページ（より古いレコード）のカーソル情報を返す
     *
     * @param array<\App\Domain\Log\PageAccessLogs\Entity\PageAccessLog> $rows
     * @return array<string, string>|null
     */
    public function getNextCursor(array $rows): ?array
    {
        if (empty($rows)) {
            return null;
        }

        // 降順表示の末尾（最古レコード）がカーソル
        $oldest = $rows[count($rows) - 1];
        $oldestAccessed = $oldest->accessed()->format('Y-m-d\TH:i:s');
        $oldestId = $oldest->id()->toStringOrNull();

        if ($oldestAccessed === null || $oldestId === null) {
            return null;
        }

        return [
            'navigation_type' => Vo\NavigationType::PREV,
            'cursor_id' => $oldestId,
            'cursor_accessed' => $oldestAccessed,
        ];
    }

    /**
     * アクセス日時の範囲を正規化して返す（from <= to となるよう調整）
     *
     * @return array{string|null, string|null}
     */
    private function resolveAccessedRange(): array
    {
        /** @var array<string, string> $data */
        $data = $this->request->getQuery();

        $fromRaw = Cast::toDateTimeStringOrNull($data['accessed_from'] ?? null, 'Y-m-d\TH:i:s');
        $toRaw = Cast::toDateTimeStringOrNull($data['accessed_to'] ?? null, 'Y-m-d\TH:i:s');

        if ($fromRaw === null || $toRaw === null) {
            return [$fromRaw, $toRaw];
        }

        $from = Cast::toDateTimeOrNull($fromRaw);
        $to = Cast::toDateTimeOrNull($toRaw);

        if ($from === null || $to === null) {
            return [$fromRaw, $toRaw];
        }

        // from > to の場合はスワップ（accessedFrom >= 用に小さい値、accessedTo <= 用に大きい値）
        if ($from > $to) {
            return [$toRaw, $fromRaw];
        }

        return [$fromRaw, $toRaw];
    }
}
