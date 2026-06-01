<?php
/* @var \App\Domain\User\UserAccounts\Entity\UserAccount $entity */
/* @var array<\App\Domain\User\UserAccounts\Entity\UserAccountHistory> $histories */

$statusLabels = [
    '100' => '仮登録',
    '200' => '有効',
    '810' => '停止',
    '820' => 'ロック',
    '900' => '削除',
];
?>
<div class="card shadow-sm mb-3">
    <div class="card-header bg-info text-white">
        詳細表示
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th style="width:30%">ID</th>
                <td><?= h($entity->id()) ?></td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td><?= h($entity->email()) ?></td>
            </tr>
            <tr>
                <th>名前</th>
                <td><?= h($entity->name()) ?></td>
            </tr>
            <tr>
                <th>アカウントステータス</th>
                <td><?= h($statusLabels[$entity->accountStatusMasterId()->toString()] ?? $entity->accountStatusMasterId()->toString()) ?></td>
            </tr>
            <tr>
                <th>メール確認</th>
                <td><?= $entity->isEmailVerified()->toInt() ? '確認済み' : '未確認' ?></td>
            </tr>
            <tr>
                <th>パスワード変更日時</th>
                <td><?= h($entity->passwordChangedAt()->format('Y/m/d H:i:s') ?? '----/--/-- --:--:--') ?></td>
            </tr>
            <tr>
                <th>パスワード有効期限</th>
                <td><?= h($entity->passwordExpiresAt()->format('Y/m/d H:i:s') ?? '----/--/-- --:--:--') ?></td>
            </tr>
        </table>

        <div class="text-center mt-4">
            <a href="<?= $this->Url->build([
                'controller' => 'Search',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>" class="btn btn-secondary px-5">戻る</a>
            <a href="<?= $this->Url->build([
                'controller' => 'Edit',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'user_account_id' => $entity->id()->toString(),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>" class="btn btn-primary px-5">更新</a>
            <a href="<?= $this->Url->build([
                'controller' => 'Create',
                'action' => 'copy',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'user_account_id' => $entity->id()->toString(),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>" class="btn btn-primary px-5">複製</a>
            <?= $this->Form->postLink('削除', [
                'controller' => 'Delete',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'user_account_id' => $entity->id()->toString(),
                '?' => $this->getRequest()->getQuery(),
            ], [
                'class' => 'btn btn-danger px-5',
                'confirm' => '削除しますか？',
            ]) ?>
        </div>
    </div>
</div>

<!-- 履歴 -->
<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">
        変更履歴
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="table-dark">
                <tr >
                    <th class="text-black-50">履歴日時</th>
                    <th class="text-black-50">操作</th>
                    <th class="text-black-50">メールアドレス</th>
                    <th class="text-black-50">名前</th>
                    <th class="text-black-50">ステータス</th>
                    <th class="text-black-50">メール確認</th>
                    <th class="text-black-50">PW変更日時</th>
                    <th class="text-black-50">PW有効期限</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($histories) === 0) { ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">履歴がありません</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($histories as $history) { ?>
                        <tr>
                            <td><?= h($history->historyCreated()->format('Y/m/d H:i:s') ?? '') ?></td>
                            <td>
                                <?php
                                $opLabel = match ($history->operationType()) {
                                    'INSERT' => '<span class="badge bg-success">作成</span>',
                                    'UPDATE' => '<span class="badge bg-primary">更新</span>',
                                    'DELETE' => '<span class="badge bg-danger">削除</span>',
                                    default => h($history->operationType()),
                                };
                                echo $opLabel;
                                ?>
                            </td>
                            <td><?= h($history->email()) ?></td>
                            <td><?= h($history->name()) ?></td>
                            <td><?= h($statusLabels[$history->accountStatusMasterId()->toString()] ?? $history->accountStatusMasterId()->toString()) ?></td>
                            <td><?= $history->isEmailVerified()->toInt() ? '確認済み' : '未確認' ?></td>
                            <td><?= h($history->passwordChangedAt()->format('Y/m/d H:i:s') ?? '') ?></td>
                            <td><?= h($history->passwordExpiresAt()->format('Y/m/d H:i:s') ?? '') ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
