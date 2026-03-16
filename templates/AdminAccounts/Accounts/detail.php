<?php

use App\Domain\Admin\AdminAccounts\ValueObject\Status;

/* @var \App\Domain\Admin\AdminAccounts\Entity\AdminAccount $entity */
/* @var array<int, array<string, mixed>> $histories */

?>
<div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white">
        詳細表示
    </div>

    <div class="card-body">

        <table class="table table-bordered">
            <tr>
                <th style="width:30%">ID</th>
                <td><?= h($entity->id()) ?></td>
            </tr>
        </table>

        <h6 class="border-bottom pb-2 mb-3">アカウント情報</h6>
        <table class="table table-bordered">
            <tr>
                <th style="width:30%">ログインID</th>
                <td><?= h($entity->loginId()) ?></td>
            </tr>
            <tr>
                <th>名前</th>
                <td><?= h($entity->name()) ?></td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td><?= h($entity->email()) ?></td>
            </tr>
        </table>

        <h6 class="border-bottom pb-2 mb-3">権限・ステータス</h6>
        <table class="table table-bordered">
            <tr>
                <th style="width:30%">権限</th>
                <td><?= h($entity->roleName()) ?></td>
            </tr>
            <tr>
                <th>ステータス</th>
                <td>
                    <span class="badge <?= $entity->status()->toInt() === Status::ACTIVE ? 'bg-success' : 'bg-secondary' ?>">
                        <?= h($entity->status()->toLabel()) ?>
                    </span>
                </td>
            </tr>
        </table>

        <div class="text-center mt-3 mb-4">
            <a href="<?= $this->Url->build([
                'controller' => 'Search',
                'action' => 'index',
                '?' => $this->getRequest()->getQuery(),
            ]) ?>" class="btn btn-secondary px-5">戻る</a>
            <a href="<?= $this->Url->build([
                'controller' => 'Edit',
                'action' => 'index',
                'admin_account_id' => $entity->id(),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>" class="btn btn-primary px-5 ms-3">更新</a>
            <?= $this->Form->postLink('削除', [
                'controller' => 'Delete',
                'action' => 'indexPost',
                'admin_account_id' => $entity->id()->toString(),
                '?' => $this->getRequest()->getQuery(),
            ], [
                'class' => 'btn btn-danger px-5 ms-3',
                'confirm' => '削除しますか？'
            ]) ?>
        </div>
    </div>
</div>

<!-- 変更履歴 -->
<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">
        変更履歴
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>操作日時</th>
                        <th>ログインID</th>
                        <th>名前</th>
                        <th>メールアドレス</th>
                        <th>ステータス</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($histories)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">履歴データがありません。</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($histories as $history): ?>
                    <tr>
                        <td><?= h($history['operated_at']) ?></td>
                        <td><?= h($history['login_id']) ?></td>
                        <td><?= h($history['name']) ?></td>
                        <td><?= h($history['email']) ?></td>
                        <td>
                            <span class="badge <?= (int)$history['status'] === Status::ACTIVE ? 'bg-success' : 'bg-secondary' ?>">
                                <?= h(Status::LABELS[(int)$history['status']] ?? '') ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach ?>
                <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
