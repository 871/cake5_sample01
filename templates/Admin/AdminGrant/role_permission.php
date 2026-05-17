<?php
/* @var array $rows */
?>
<div class="card mb-3">
    <div class="card-header bg-secondary text-white">ロール権限検索</div>
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search_text" class="form-control" value="<?= h($this->getRequest()->getQuery('search_text')) ?>" placeholder="キーワード">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">検索</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>検索結果</span>
        <a href="<?= $this->Url->build(['controller' => 'RolePermission/Create', 'action' => 'index', 'account_id' => $this->getRequest()->getParam('account_id'), '?' => $this->getRequest()->getQuery()]) ?>" class="btn btn-success btn-sm">新規作成</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th><?= $this->Paginator->sort('GrantRolePermissions.grant_role_id', 'ロールID') ?></th>
                    <th>ロール名</th>
                    <th><?= $this->Paginator->sort('GrantRolePermissions.grant_permission_id', '権限ID') ?></th>
                    <th>権限名</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($rows) === 0) { ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">データがありません</td></tr>
                <?php } else { ?>
                    <?php foreach ($rows as $row) { ?>
                        <tr>
                            <td><?= h((string)$row->grant_role_id) ?></td>
                            <td><?= h((string)($row->grant_role->name ?? '')) ?></td>
                            <td><?= h((string)$row->grant_permission_id) ?></td>
                            <td><?= h((string)($row->grant_permission->name ?? '')) ?></td>
                            <td class="text-nowrap">
                                <a href="<?= $this->Url->build(['controller' => 'RolePermission/Detail', 'action' => 'index', 'account_id' => $this->getRequest()->getParam('account_id'), 'grant_role_permission_id' => $row->id, '?' => $this->getRequest()->getQuery()]) ?>" class="btn btn-info btn-sm">詳細</a>
                                <a href="<?= $this->Url->build(['controller' => 'RolePermission/Update', 'action' => 'index', 'account_id' => $this->getRequest()->getParam('account_id'), 'grant_role_id' => $row->grant_role_id, '?' => $this->getRequest()->getQuery()]) ?>" class="btn btn-primary btn-sm">更新</a>
                                <?= $this->Form->postLink('削除', ['controller' => 'RolePermission/Delete', 'action' => 'indexPost', 'account_id' => $this->getRequest()->getParam('account_id'), 'grant_role_permission_id' => $row->id, '?' => $this->getRequest()->getQuery()], ['class' => 'btn btn-danger btn-sm', 'confirm' => '削除しますか？']) ?>
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
