<?php
/** @var \App\Domain\Admin\AdminGrant\Entity\GrantRole $entity */
/** @var array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission> $grantPermissionOptions */
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
        <div class="mt-4">
            <h5>アカウント設定</h5>
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 15%">ユーザID</th>
                        <th style="width: 20%">ユーザ名</th>
                        <th style="width: 30%">メールアドレス</th>
                        <th>ステータス</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($entity->grantAccountRoles() === []) { ?>
                    <tr><td colspan="4" class="text-center text-muted">紐づくアカウントはありません</td></tr>
                <?php } ?>
                <?php foreach ($entity->grantAccountRoles() as $grantAccountRole) { ?>
                    <?php $userAccountGrant = $grantAccountRole->userAccountGrant(); ?>
                    <tr>
                        <td><?= h($grantAccountRole->userAccountId()->toString()) ?></td>
                        <td><?= h($userAccountGrant?->name()->toString() ?? '') ?></td>
                        <td><?= h($userAccountGrant?->email()->toString() ?? '') ?></td>
                        <td><?= h($userAccountGrant?->accountStatusMasterName()->toString() ?? '') ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <h5>権限設定</h5>
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 100px;">権限ID</th>
                        <th>権限コード</th>
                        <th>権限名</th>
                        <th>説明</th>
                        <th style="width: 150px;">設定</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grantPermissionOptions as $option) { ?>
                        <tr>
                            <td><?= h($option->grantPermissionId()->toString()) ?></td>
                            <td><?= h($option->code()->toString()) ?></td>
                            <td><?= h($option->name()->toString()) ?></td>
                            <td><?= h($option->description()->toString()) ?></td>
                            <td><?= $entity->hasPermissionCode($option->code()) ? 'あり' : 'なし' ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="text-center mt-4">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/UserGrant/Role',
                'controller' => 'Search',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>" class="btn btn-secondary px-5">戻る</a>
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/UserGrant/Role',
                'controller' => 'Edit',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'grant_role_id' => $entity->grantRoleId()->toString(),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>" class="btn btn-primary px-5">更新</a>
        </div>
    </div>
</div>
