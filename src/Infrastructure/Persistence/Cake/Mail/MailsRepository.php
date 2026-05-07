<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Cake\Mail;

use App\Domain\Mail\Entity\Mail as DomainEntity;
use App\Domain\Mail\Entity\MailBounceLog;
use App\Domain\Mail\Entity\MailReceivedCheckLog;
use App\Domain\Mail\Entity\MailSentLog;
use App\Domain\Mail\Repository\MailsRepository as DomainMailsRepository;
use App\Domain\Mail\SearchCondition;
use App\Domain\Mail\ValueObject\Id;

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
     * 送信済みステータスへ更新（送信ログを作成）
     *
     * @param \App\Domain\Mail\Entity\MailSentLog $logEntity
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function updateSent(MailSentLog $logEntity): DomainEntity
    {
        return (new MailsRepository\UpdateSent($logEntity))->run();
    }

    /**
     * 送信失敗ステータスへ更新（送信ログを作成）
     *
     * @param \App\Domain\Mail\Entity\MailSentLog $logEntity
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function updateFailed(MailSentLog $logEntity): DomainEntity
    {
        return (new MailsRepository\UpdateFailed($logEntity))->run();
    }

    /**
     * 受信確認済みステータスへ更新（受信確認ログを作成）
     *
     * @param \App\Domain\Mail\Entity\MailReceivedCheckLog $logEntity
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function updateReceived(MailReceivedCheckLog $logEntity): DomainEntity
    {
        return (new MailsRepository\UpdateReceived($logEntity))->run();
    }

    /**
     * バウンス確認済みステータスへ更新（バウンスログを作成）
     *
     * @param \App\Domain\Mail\Entity\MailBounceLog $logEntity
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function updateBounced(MailBounceLog $logEntity): DomainEntity
    {
        return (new MailsRepository\UpdateBounced($logEntity))->run();
    }
}
