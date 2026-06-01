<?php
/** @var iterable<\App\Model\Entity\Grant\GrantRole> $rows */
/** @var array<\App\Domain\User\UserGrant\Entity\GrantPermission> $grantPermissionOptions */

$pageOptions = [
    'url' => [
        'account_id' => $this->getRequest()->getParam('account_id'),
    ],
];

$selectedIsActives = (array)$this->getRequest()->getQuery('is_active', ['1']);
$selectedGrantPermissionIds = (array)$this->getRequest()->getQuery('grant_permission_id', []);
?>
<div class="card mb-3">
    <div class="card-header bg-secondary text-white">ロール権限検索</div>
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">キーワード</label>
                <input type="text" name="keyword" class="form-control" value="<?= h((string)$this->getRequest()->getQuery('keyword')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">有効状態</label>
                <div class="form-control">
                    <label class="form-check-label me-3">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="is_active[]"
                            value="1"
                            <?= in_array('1', $selectedIsActives, true) ? 'checked' : '' ?>
                        >有効
                    </label>
                    <label class="form-check-label">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="is_active[]"
                            value="0"
                            <?= in_array('0', $selectedIsActives, true) ? 'checked' : '' ?>
                        >無効
                    </label>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">権限</label>
                <select name="grant_permission_id[]" class="form-select" multiple size="4">
                    <?php foreach ($grantPermissionOptions as $option) { ?>
                        <option
                            value="<?= h($option->grantPermissionId()->toString()) ?>"
                            <?= in_array($option->grantPermissionId()->toString(), $selectedGrantPermissionIds, true) ? 'selected' : '' ?>
                        ><?= h($option->name()->toString()) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
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
                    <th><?= $this->Paginator->sort('GrantRoles.id', 'ID', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('GrantRoles.code', 'コード', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('GrantRoles.name', '名称', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('GrantRoles.sort', '並び順', $pageOptions) ?></th>
                    <th>状態</th>
                    <th><?= $this->Paginator->sort('GrantRoles.modified', '更新日時', $pageOptions) ?></th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($rows) === 0) { ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">データがありません</td></tr>
                <?php } else { ?>
                    <?php foreach ($rows as $row) { ?>
                        <tr>
                            <td><?= h((string)$row->id) ?></td>
                            <td><?= h((string)$row->code) ?></td>
                            <td><?= h((string)$row->name) ?></td>
                            <td><?= h((string)$row->sort) ?></td>
                            <td><?= (int)$row->is_active === 1 ? '<span class="badge bg-success">有効</span>' : '<span class="badge bg-secondary">無効</span>' ?></td>
                            <td><?= h((string)$row->modified?->format('Y-m-d H:i:s')) ?></td>
                            <td class="text-nowrap">
                                <a href="<?= $this->Url->build([
                                    'prefix' => 'Admin/UserGrant/Role',
                                    'controller' => 'Detail',
                                    'action' => 'index',
                                    'account_id' => $this->getRequest()->getParam('account_id'),
                                    'grant_role_id' => $row->id,
                                    '?' => $this->getRequest()->getQuery(),
                                ]) ?>" class="btn btn-info btn-sm">詳細</a>
                                <a href="<?= $this->Url->build([
                                    'prefix' => 'Admin/UserGrant/Role',
                                    'controller' => 'Edit',
                                    'action' => 'index',
                                    'account_id' => $this->getRequest()->getParam('account_id'),
                                    'grant_role_id' => $row->id,
                                    '?' => $this->getRequest()->getQuery(),
                                ]) ?>" class="btn btn-primary btn-sm">更新</a>
                                <?php if (count($row->grant_account_roles ?? []) > 0) { ?>
                                   <a class="btn btn-danger btn-sm disabled" aria-disabled="true" tabindex="-1">削除</a>
                                <?php } else { ?>
                                   <?= $this->Form->postLink('削除', [
                                       'prefix' => 'Admin/UserGrant/Role',
                                       'controller' => 'Delete',
                                       'action' => 'index',
                                       'account_id' => $this->getRequest()->getParam('account_id'),
                                       'grant_role_id' => $row->id,
                                       '?' => $this->getRequest()->getQuery(),
                                   ], [
                                       'class' => 'btn btn-danger btn-sm',
                                       'confirm' => '削除しますか？',
                                   ]) ?>
                                <?php } ?>
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
