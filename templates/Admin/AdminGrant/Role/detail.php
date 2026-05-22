<?php
/** @var \App\Domain\Admin\AdminGrant\Entity\GrantRole $entity */
?>
<div class="card shadow-sm">
    <div class="card-header bg-info text-white">ロール権限詳細</div>
    <div class="card-body">
        <table class="table table-bordered mb-0">
            <tr><th style="width: 25%">ID</th><td><?= h($entity->grantRoleId()->toString()) ?></td></tr>
            <tr><th>コード</th><td><?= h($entity->code()->toString()) ?></td></tr>
            <tr><th>名称</th><td><?= h($entity->name()->toString()) ?></td></tr>
            <tr><th>説明</th><td><?= h($entity->description()->toString()) ?></td></tr>
            <tr><th>並び順</th><td><?= h($entity->sort()->toString()) ?></td></tr>
            <tr><th>有効状態</th><td><?= $entity->isActive()->toInt() === 1 ? '有効' : '無効' ?></td></tr>
        </table>
        <div class="text-center mt-4">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/AdminGrant/Role',
                'controller' => 'Search',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>" class="btn btn-secondary px-5">戻る</a>
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/AdminGrant/Role',
                'controller' => 'Edit',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'grant_role_id' => $entity->grantRoleId()->toString(),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>" class="btn btn-primary px-5">更新</a>
        </div>
    </div>
</div>
