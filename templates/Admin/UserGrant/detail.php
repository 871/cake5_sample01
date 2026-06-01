<?php

/** @var \App\Domain\User\UserGrant\Entity\UserAccountGrant $userAccountGrant */
/** @var array<\App\Domain\User\UserGrant\Entity\GrantPermission> $grantPermissions */


?>
<div class="card">
    <div class="card-header bg-info text-white">
        ユーザ権限詳細
        （
        ユーザID：<span class="badge bg-light text-dark"><?= h($userAccountGrant->userAccountId()) ?></span>
        ステータス：
        <?= match($userAccountGrant->accountStatusMasterCode()->toString()) {
            'PENDING' => '<span class="badge bg-primary">' . h($userAccountGrant->accountStatusMasterName()) . '</span>',
            'ACTIVE' => '<span class="badge bg-success">' . h($userAccountGrant->accountStatusMasterName()) . '</span>',
            'SUSPENDED' => '<span class="badge bg-warning">' . h($userAccountGrant->accountStatusMasterName()) . '</span>',
            'LOCKED' => '<span class="badge bg-danger">' . h($userAccountGrant->accountStatusMasterName()) . '</span>',
            'DELETED' => '<span class="badge bg-secondary">' . h($userAccountGrant->accountStatusMasterName()) . '</span>',
            default => '<span class="badge bg-secondary">' . h($userAccountGrant->accountStatusMasterName()) . '</span>',
        } ?>  
        ユーザ名：<span class="badge bg-light text-dark"><?= h($userAccountGrant->name()) ?></span>
        メールアドレス：<span class="badge bg-light text-dark"><?= h($userAccountGrant->email()) ?></span>
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
                    <?= $userAccountGrant->hasPermissionCode($grantPermission->code()) ? '' :'class="table-secondary"' ?>
                >
                    <td
                        title="ID: <?= h($grantPermission->grantPermissionId()) ?>"
                    >
                        <?= h($grantPermission->code()) ?>
                    </td>
                    <td><?= h($grantPermission->name()) ?></td>
                    <td><?= $userAccountGrant->hasPermissionCode($grantPermission->code()) ? '<span class="badge bg-success">あり</span>' : '<span class="badge bg-danger">なし</span>' ?></td>
                    <td>
                        <?= join(
                                '<br>', 
                                array_map(
                                    fn($setting) => h($setting), 
                                    $userAccountGrant->grantSettings($grantPermission->code())
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
            'user_account_id' => $userAccountGrant->userAccountId(), 
            '?' => $this->getRequest()->getQuery(),
        ]) ?>" class="btn btn-primary px-5">更新</a>
    </div>
</div>
