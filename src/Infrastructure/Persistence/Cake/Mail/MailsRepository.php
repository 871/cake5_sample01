<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail;

use App\Domain\Mail\Entity\Mail as DomainEntity;
use App\Domain\Mail\Repository\MailsRepository as DomainMailsRepository;
use App\Domain\Mail\SearchCondition;
use App\Domain\Mail\ValueObject\Id;
use DateTimeImmutable;

final class MailsRepository implements DomainMailsRepository
{
    /**
     * 作成
     *
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function create(DomainEntity $entity): DomainEntity
    {
        return (new MailsRepository\Create($entity))->run();
    }

    /**
     * 検索
     *
     * @param \App\Domain\Mail\SearchCondition $condition
     * @return array<\App\Domain\Mail\Entity\Mail>
     */
    public function search(SearchCondition $condition): array
    {
        return (new MailsRepository\Search($condition))->run();
    }

    /**
     * 詳細取得
     *
     * @param \App\Domain\Mail\ValueObject\Id $id
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function read(Id $id): DomainEntity
    {
        return (new MailsRepository\Read($id))->run();
    }

    /**
     * 送信待ちメールを送信し、送信ログ保存とステータス更新を行う
     *
     * @param \DateTimeImmutable $now 現在日時
     * @return int 処理件数
     */
    public function sendWaitingMails(DateTimeImmutable $now): int
    {
        return (new MailsRepository\SendWaiting())->run($now);
    }

    /**
     * 受信確認IMAPサーバを確認し、受信確認ログ保存とステータス更新を行う
     *
     * @param \DateTimeImmutable $now 現在日時
     * @return int 処理件数
     */
    public function checkReceivedMails(DateTimeImmutable $now): int
    {
        return (new MailsRepository\CheckReceived())->run($now);
    }

    /**
     * バウンスIMAPサーバを確認し、バウンスログ保存とステータス更新を行う
     *
     * @param \DateTimeImmutable $now 現在日時
     * @return int 処理件数
     */
    public function checkBouncedMails(DateTimeImmutable $now): int
    {
        return (new MailsRepository\CheckBounced())->run($now);
    }
}
