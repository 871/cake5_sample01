<?php

/** @var \App\Domain\Admin\AdminGrant\Entity\AdminAccountGrant $adminAccountGrant */
/** @var \App\Domain\Admin\AdminGrant\Entity\GrantPermission[] $grantPermissions */


?>
<div class="card">
    <div class="card-header bg-info text-white">
        管理者権限詳細
        （
        管理者ID：<span class="badge bg-light text-dark"><?= h($adminAccountGrant->adminAccountId()) ?></span>
        管理者名：<span class="badge bg-light text-dark"><?= h($adminAccountGrant->name()) ?></span>
        メールアドレス：<span class="badge bg-light text-dark"><?= h($adminAccountGrant->email()) ?></span>
        ）
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="table-dark">
                <tr>
                    <th>コード</th>
                    <th>権限名</th>
                    <th>付与状況</th>
                    <th>付与設定</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($grantPermissions as $grantPermission) { ?>
                <tr
                    <?= $adminAccountGrant->hasPermissionCode($grantPermission->code()) ? '' :'class="table-secondary"' ?>
                >
                    <td
                        title="ID: <?= h($grantPermission->grantPermissionId()) ?>"
                    >
                        <?= h($grantPermission->code()) ?>
                    </td>
                    <td><?= h($grantPermission->name()) ?></td>
                    <td><?= $adminAccountGrant->hasPermissionCode($grantPermission->code()) ? '<span class="badge bg-success">あり</span>' : '<span class="badge bg-danger">なし</span>' ?></td>
                    <td>
                        <?= join(
                                '<br>', 
                                array_map(
                                    fn($setting) => h($setting), 
                                    $adminAccountGrant->grantSettings($grantPermission->code())
                                )
                        ) ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer text-center">
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
            'admin_account_id' => $adminAccountGrant->adminAccountId(), 
            '?' => $this->getRequest()->getQuery(),
        ]) ?>" class="btn btn-primary px-5">更新</a>
    </div>
</div>
