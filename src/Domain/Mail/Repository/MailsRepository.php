<?php
declare(strict_types=1);

namespace App\Domain\Mail\Repository;

use App\Domain\Mail\Entity\Mail;
use App\Domain\Mail\Entity\MailBounceLog;
use App\Domain\Mail\Entity\MailReceivedCheckLog;
use App\Domain\Mail\Entity\MailSentLog;
use App\Domain\Mail\SearchCondition;
use App\Domain\Mail\ValueObject\Id;

interface MailsRepository
{
    /**
     * 作成
     *
     * @param \App\Domain\Mail\Entity\Mail $entity
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function create(Mail $entity): Mail;

    /**
     * 検索
     *
     * @param \App\Domain\Mail\SearchCondition $condition
     * @return array<\App\Domain\Mail\Entity\Mail>
     */
    public function search(SearchCondition $condition): array;

    /**
     * 詳細取得
     *
     * @param \App\Domain\Mail\ValueObject\Id $id
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function read(Id $id): Mail;

    /**
     * 送信済みステータスへ更新（送信ログを作成）
     *
     * @param \App\Domain\Mail\Entity\MailSentLog $logEntity
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function updateSent(MailSentLog $logEntity): Mail;

    /**
     * 送信失敗ステータスへ更新（送信ログを作成）
     *
     * @param \App\Domain\Mail\Entity\MailSentLog $logEntity
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function updateFailed(MailSentLog $logEntity): Mail;

    /**
     * 受信確認済みステータスへ更新（受信確認ログを作成）
     *
     * @param \App\Domain\Mail\Entity\MailReceivedCheckLog $logEntity
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function updateReceived(MailReceivedCheckLog $logEntity): Mail;

    /**
     * バウンス確認済みステータスへ更新（バウンスログを作成）
     *
     * @param \App\Domain\Mail\Entity\MailBounceLog $logEntity
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function updateBounced(MailBounceLog $logEntity): Mail;
}
