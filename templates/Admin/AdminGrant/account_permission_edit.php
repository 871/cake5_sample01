<?php
/* @var string $adminAccountId */
/* @var array $grantRoleOptions */
/* @var array $grantPermissionOptions */
/* @var array $grantedRoleIds */
/* @var array $grantedPermissionIds */
?>
<div class="card">
    <div class="card-header bg-info text-white">管理者権限更新（管理者ID: <?= h($adminAccountId) ?>）</div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">

            <div class="mb-4">
                <label class="form-label">ロール権限</label>
                <div class="row">
                    <?php foreach ($grantRoleOptions as $option) { ?>
                        <div class="col-md-4">
                            <label>
                                <input type="checkbox" name="grant_role_ids[]" value="<?= h($option['value']) ?>" <?= in_array($option['value'], $grantedRoleIds, true) ? 'checked' : '' ?>>
                                <?= h($option['label']) ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">個別権限</label>
                <div class="row">
                    <?php foreach ($grantPermissionOptions as $option) { ?>
                        <div class="col-md-4">
                            <label>
                                <input type="checkbox" name="grant_permission_ids[]" value="<?= h($option['value']) ?>" <?= in_array($option['value'], $grantedPermissionIds, true) ? 'checked' : '' ?>>
                                <?= h($option['label']) ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="text-center">
                <a href="<?= $this->Url->build(['action' => 'detail', 'account_id' => $this->getRequest()->getParam('account_id'), 'admin_account_id' => $adminAccountId, '?' => $this->getRequest()->getQuery()]) ?>" class="btn btn-secondary px-5">戻る</a>
                <button type="submit" class="btn btn-primary px-5">更新</button>
            </div>
        </form>
    </div>
</div>
