<?php
/* @var array $rows */
/* @var array $accountStatusOptions */
/* @var array $grantRoleOptions */
/* @var array $grantPermissionOptions */
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
                <label class="form-label">ステータス</label>
                <select name="account_status_master_id" class="form-select">
                    <option value="">（全て）</option>
                    <?php foreach ($accountStatusOptions as $option) { ?>
                        <option value="<?= h($option['value']) ?>" <?= $this->getRequest()->getQuery('account_status_master_id') === $option['value'] ? 'selected' : '' ?>><?= h($option['label']) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">ロール</label>
                <select name="grant_role_id" class="form-select">
                    <option value="">（全て）</option>
                    <?php foreach ($grantRoleOptions as $option) { ?>
                        <option value="<?= h($option['value']) ?>" <?= $this->getRequest()->getQuery('grant_role_id') === $option['value'] ? 'selected' : '' ?>><?= h($option['label']) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">権限</label>
                <select name="grant_permission_id" class="form-select">
                    <option value="">（全て）</option>
                    <?php foreach ($grantPermissionOptions as $option) { ?>
                        <option value="<?= h($option['value']) ?>" <?= $this->getRequest()->getQuery('grant_permission_id') === $option['value'] ? 'selected' : '' ?>><?= h($option['label']) ?></option>
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
                    <th><?= $this->Paginator->sort('AdminAccounts.id', 'ID') ?></th>
                    <th><?= $this->Paginator->sort('AdminAccounts.email', 'メール') ?></th>
                    <th><?= $this->Paginator->sort('AdminAccounts.name', '名前') ?></th>
                    <th>権限</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($rows) === 0) { ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">データがありません</td></tr>
                <?php } else { ?>
                    <?php foreach ($rows as $row) { ?>
                        <tr>
                            <td><?= h((string)$row->id) ?></td>
                            <td><?= h((string)$row->email) ?></td>
                            <td><?= h((string)$row->name) ?></td>
                            <td><?= h((string)$row->grant_permission_name) ?></td>
                            <td class="text-nowrap">
                                <a href="<?= $this->Url->build(['controller' => 'Detail', 'action' => 'index', 'account_id' => $this->getRequest()->getParam('account_id'), 'admin_account_id' => $row->id, '?' => $this->getRequest()->getQuery()]) ?>" class="btn btn-info btn-sm">詳細</a>
                                <a href="<?= $this->Url->build(['controller' => 'Edit', 'action' => 'index', 'account_id' => $this->getRequest()->getParam('account_id'), 'admin_account_id' => $row->id, '?' => $this->getRequest()->getQuery()]) ?>" class="btn btn-primary btn-sm">更新</a>
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
            <?= $this->Paginator->prev('«') ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next('»') ?>
        </ul>
    </div>
</div>
