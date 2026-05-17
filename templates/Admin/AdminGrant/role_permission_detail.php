<?php
/* @var \App\Domain\Admin\AdminGrant\Entity\GrantRolePermission $entity */
?>
<div class="card">
    <div class="card-header bg-info text-white">ロール権限詳細</div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>ID</th><td><?= h($entity->id()) ?></td></tr>
            <tr><th>ロールID</th><td><?= h($entity->grantRoleId()) ?></td></tr>
            <tr><th>権限ID</th><td><?= h($entity->grantPermissionId()) ?></td></tr>
            <tr><th>作成日時</th><td><?= h($entity->created()->format('Y/m/d H:i:s')) ?></td></tr>
            <tr><th>更新日時</th><td><?= h($entity->modified()->format('Y/m/d H:i:s')) ?></td></tr>
        </table>
        <div class="text-center">
            <a href="<?= $this->Url->build(['action' => 'search', 'account_id' => $this->getRequest()->getParam('account_id'), '?' => $this->getRequest()->getQuery()]) ?>" class="btn btn-secondary px-5">戻る</a>
        </div>
    </div>
</div>
