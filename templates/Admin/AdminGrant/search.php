<?php
/** @var array $rows */
/** @var array<\App\Domain\Admin\AdminGrant\Entity\AccountStatusMaster> $accountStatusOptions */
/** @var array<\App\Domain\Admin\AdminGrant\Entity\GrantRole> $grantRoleOptions */
/** @var array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission> $grantPermissionOptions */

$pageOptions = [
    'url' => [
        'account_id' => $this->getRequest()->getParam('account_id'),
    ],
];
?>
<div class="card mb-3">
    <div class="card-header bg-secondary text-white">管理者権限検索</div>
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">管理者ID</label>
                <input type="number" min="1" name="admin_account_id" class="form-control" value="<?= h($this->getRequest()->getQuery('admin_account_id')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">管理者ステータス</label>
                <select 
                    name="account_status_master_id[]" 
                    class="form-select"
                    multiple
                >
                    <?php foreach ($accountStatusOptions as $option) { ?>
                        <option
                            value="<?= h($option->accountStatusMasterId()->toString()) ?>"
                            <?= in_array($option->accountStatusMasterId()->toString(), (array)$this->getRequest()->getQuery('account_status_master_id')) ? 'selected' : '' ?>
                        ><?= h($option->accountStatusMasterName()->toString()) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">ロール</label>
                <select 
                    name="grant_role_id[]" 
                    class="form-select"
                    multiple
                >
                    <?php foreach ($grantRoleOptions as $option) { ?>
                        <option
                            value="<?= h($option->grantRoleId()->toString()) ?>"
                            <?= in_array($option->grantRoleId()->toString(), (array)$this->getRequest()->getQuery('grant_role_id')) ? 'selected' : '' ?>
                        ><?= h($option->name()->toString()) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">権限</label>
                <select 
                    name="grant_permission_id[]" 
                    class="form-select"
                    multiple
                >
                    <?php foreach ($grantPermissionOptions as $option) { ?>
                        <option
                            value="<?= h($option->grantPermissionId()->toString()) ?>"
                            <?= in_array($option->grantPermissionId()->toString(), (array)$this->getRequest()->getQuery('grant_permission_id')) ? 'selected' : '' ?>
                        ><?= h($option->name()->toString()) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">検索</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">検索結果</div>
    <div class="card-body p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>
                        <?= $this->Paginator->sort('AdminAccounts.id', '管理者ID', $pageOptions) ?>
                        <br>
                        ステータス
                    </th>
                    <th>
                        <?= $this->Paginator->sort('AdminAccounts.name', '管理者名', $pageOptions) ?>
                        <br>
                        <?= $this->Paginator->sort('AdminAccounts.email', 'メール', $pageOptions) ?>
                    </th>
                    <th>
                        権限名
                        <br>
                        権限コード
                    </th>
                    <th>権限設定</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($rows) === 0) { ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">データがありません</td></tr>
                <?php } else { ?>
                    <?php foreach ($rows as $row) { ?>
                        <tr
                            <?= $row->role_grant_exists || $row->account_grant_exists? '' :'class="table-secondary"' ?>
                        >
                            <td>
                                <?= h((string)$row->id) ?>
                                <br>
                                <?= match($row->account_status_master_code) {
                                    'PENDING' => '<span class="badge bg-primary">' . h($row->account_status_master_name) . '</span>',
                                    'ACTIVE' => '<span class="badge bg-success">' . h($row->account_status_master_name) . '</span>',
                                    'SUSPENDED' => '<span class="badge bg-warning">' . h($row->account_status_master_name) . '</span>',
                                    'LOCKED' => '<span class="badge bg-danger">' . h($row->account_status_master_name) . '</span>',
                                    'DELETED' => '<span class="badge bg-secondary">' . h($row->account_status_master_name) . '</span>',
                                    default => '<span class="badge bg-secondary">' . h($row->account_status_master_name) . '</span>',
                                } ?>
                            </td>
                            <td>
                                <?= h((string)$row->name) ?>
                                <br>
                                <?= h((string)$row->email) ?>
                            </td>
                            <td>
                                <?= h((string)$row->grant_permission_name) ?>
                                <br>
                                <?= h((string)$row->grant_permission_code) ?>
                            </td>
                            <td>
                                アカウント：<?= $row->role_grant_exists ? '<span class="badge bg-success">あり</span>' : '<span class="badge bg-danger">なし</span>' ?>
                                <br>
                                ロール　　：<?= $row->account_grant_exists ? '<span class="badge bg-success">あり</span>' : '<span class="badge bg-danger">なし</span>' ?>
                            </td>
                            <td class="text-nowrap">
                                <a href="<?= $this->Url->build([
                                    'controller' => 'Detail',
                                    'action' => 'index',
                                    'account_id' => $this->getRequest()->getParam('account_id'),
                                    'admin_account_id' => $row->id,
                                    '?' => $this->getRequest()->getQuery()
                                ]) ?>" class="btn btn-info btn-sm">詳細</a>
                                <a href="<?= $this->Url->build([
                                    'controller' => 'Edit',
                                    'action' => 'index',
                                    'account_id' => $this->getRequest()->getParam('account_id'),
                                    'admin_account_id' => $row->id,
                                    '?' => $this->getRequest()->getQuery()
                                ]) ?>" class="btn btn-primary btn-sm">更新</a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <div><?= $this->Paginator->counter('全 {{count}} 件中 {{start}}-{{end}} 件') ?></div>
        <ul class="pagination pagination-sm mb-0">
            <?= $this->Paginator->prev('«', $pageOptions) ?>
            <?= $this->Paginator->numbers($pageOptions) ?>
            <?= $this->Paginator->next('»', $pageOptions) ?>
        </ul>
    </div>
</div>
