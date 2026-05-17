<?php
/* @var string $adminAccountId */
/* @var array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission> $permissions */
?>
<div class="card">
    <div class="card-header bg-info text-white">管理者権限詳細（管理者ID: <?= h($adminAccountId) ?>）</div>
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="table-dark">
                <tr>
                    <th>権限ID</th>
                    <th>コード</th>
                    <th>権限名</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($permissions) === 0) { ?>
                    <tr><td colspan="3" class="text-center text-muted py-3">権限がありません</td></tr>
                <?php } else { ?>
                    <?php foreach ($permissions as $permission) { ?>
                        <tr>
                            <td><?= h($permission->id()) ?></td>
                            <td><?= h($permission->code()) ?></td>
                            <td><?= h($permission->name()) ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer text-center">
        <a href="<?= $this->Url->build(['action' => 'search', 'account_id' => $this->getRequest()->getParam('account_id'), '?' => $this->getRequest()->getQuery()]) ?>" class="btn btn-secondary px-5">戻る</a>
        <a href="<?= $this->Url->build(['action' => 'edit', 'account_id' => $this->getRequest()->getParam('account_id'), 'admin_account_id' => $adminAccountId, '?' => $this->getRequest()->getQuery()]) ?>" class="btn btn-primary px-5">更新</a>
    </div>
</div>
