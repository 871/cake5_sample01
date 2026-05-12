<?php
/* @var \Cake\View\View $this */
/* @var \App\Domain\Mail\Entity\Mail $entity */
?>
<div class="card shadow-sm mb-3">
    <div class="card-header bg-info text-white">
        メール詳細
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th style="width:30%">ID</th>
                <td><?= h($entity->id()->toStringOrNull() ?? '') ?></td>
            </tr>
            <tr>
                <th>送信ステータス</th>
                <td><?= h($entity->sendStatus()->toString()) ?></td>
            </tr>
            <tr>
                <th>送信予定日時</th>
                <td><?= h($entity->sendScheduledAt()->format('Y/m/d H:i:s') ?? '') ?></td>
            </tr>
            <tr>
                <th>件名</th>
                <td><?= h($entity->title()->toStringOrNull() ?? '') ?></td>
            </tr>
            <tr>
                <th>本文</th>
                <td style="white-space:pre-wrap"><?= h($entity->body()->toStringOrNull() ?? '') ?></td>
            </tr>
            <tr>
                <th>宛先（To）</th>
                <td><?= h($entity->mailTo()->toStringOrNull() ?? '') ?></td>
            </tr>
            <tr>
                <th>宛先（Cc）</th>
                <td><?= h($entity->mailCc()->toStringOrNull() ?? '') ?></td>
            </tr>
            <tr>
                <th>宛先（Bcc）</th>
                <td><?= h($entity->mailBcc()->toStringOrNull() ?? '') ?></td>
            </tr>
            <tr>
                <th>受信確認アドレス</th>
                <td><?= h($entity->mailReceivedCheck()->toStringOrNull() ?? '') ?></td>
            </tr>
            <tr>
                <th>返送先アドレス</th>
                <td><?= h($entity->mailReturnPath()->toStringOrNull() ?? '') ?></td>
            </tr>
            <tr>
                <th>オリジナルメッセージID</th>
                <td><?= h($entity->originalMessageId()->toStringOrNull() ?? '') ?></td>
            </tr>
            <tr>
                <th>関連データキー</th>
                <td><?= h($entity->relatedDataKey()->toStringOrNull() ?? '') ?></td>
            </tr>
            <tr>
                <th>作成日時</th>
                <td><?= h($entity->created()->format('Y/m/d H:i:s') ?? '') ?></td>
            </tr>
            <tr>
                <th>作成者</th>
                <td><?= h($entity->createdBy()->toStringOrNull() ?? '') ?></td>
            </tr>
            <tr>
                <th>作成IP</th>
                <td><?= h($entity->createdIp()->toStringOrNull() ?? '') ?></td>
            </tr>
            <tr>
                <th>更新日時</th>
                <td><?= h($entity->modified()->format('Y/m/d H:i:s') ?? '') ?></td>
            </tr>
            <tr>
                <th>更新者</th>
                <td><?= h($entity->modifiedBy()->toStringOrNull() ?? '') ?></td>
            </tr>
            <tr>
                <th>更新IP</th>
                <td><?= h($entity->modifiedIp()->toStringOrNull() ?? '') ?></td>
            </tr>
        </table>
    </div>
</div>

<?php if (count($entity->mailSentLogs()) > 0): ?>
<div class="card shadow-sm mb-3">
    <div class="card-header bg-secondary text-white">
        送信ログ
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>送信ステータス</th>
                    <th>送信日時</th>
                    <th>オリジナルメッセージID</th>
                    <th>エラーメッセージ</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($entity->mailSentLogs() as $log): ?>
                <?php /** @var \App\Domain\Mail\Entity\MailSentLog $log */ ?>
                <tr>
                    <td><?= h($log->id()->toStringOrNull() ?? '') ?></td>
                    <td><?= h($log->sendStatus()->toString()) ?></td>
                    <td><?= h($log->sentAt()->format('Y/m/d H:i:s') ?? '') ?></td>
                    <td><?= h($log->originalMessageId()->toStringOrNull() ?? '') ?></td>
                    <td><?= h($log->errorMessage()->toStringOrNull() ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (count($entity->mailReceivedCheckLogs()) > 0): ?>
<div class="card shadow-sm mb-3">
    <div class="card-header bg-secondary text-white">
        受信確認ログ
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>確認アドレス</th>
                    <th>確認日時</th>
                    <th>オリジナルメッセージID</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($entity->mailReceivedCheckLogs() as $log): ?>
                <?php /** @var \App\Domain\Mail\Entity\MailReceivedCheckLog $log */ ?>
                <tr>
                    <td><?= h($log->id()->toStringOrNull() ?? '') ?></td>
                    <td><?= h($log->checkedAddress()->toStringOrNull() ?? '') ?></td>
                    <td><?= h($log->checkedAt()->format('Y/m/d H:i:s') ?? '') ?></td>
                    <td><?= h($log->originalMessageId()->toStringOrNull() ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (count($entity->mailBounceLogs()) > 0): ?>
<div class="card shadow-sm mb-3">
    <div class="card-header bg-secondary text-white">
        バウンスログ
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>バウンスメール</th>
                    <th>バウンス種別</th>
                    <th>ステータスコード</th>
                    <th>診断コード</th>
                    <th>バウンス日時</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($entity->mailBounceLogs() as $log): ?>
                <?php /** @var \App\Domain\Mail\Entity\MailBounceLog $log */ ?>
                <tr>
                    <td><?= h($log->id()->toStringOrNull() ?? '') ?></td>
                    <td><?= h($log->bouncedEmail()->toStringOrNull() ?? '') ?></td>
                    <td><?= h($log->bounceType()->toStringOrNull() ?? '') ?></td>
                    <td><?= h($log->statusCode()->toStringOrNull() ?? '') ?></td>
                    <td><?= h($log->diagnosticCode()->toStringOrNull() ?? '') ?></td>
                    <td><?= h($log->bouncedAt()->format('Y/m/d H:i:s') ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
