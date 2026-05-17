<?php
/* @var array $grantRoleOptions */
/* @var array $grantPermissionOptions */
/* @var array $selectedPermissionIds */
/* @var bool $isEdit */
/* @var string|null $targetGrantRoleId */
?>
<div class="card">
    <div class="card-header bg-info text-white"><?= $isEdit ? 'ロール権限更新' : 'ロール権限新規作成' ?></div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">

            <div class="mb-3">
                <label class="form-label">ロール</label>
                <?php if ($isEdit) { ?>
                    <input type="text" class="form-control" value="<?= h($targetGrantRoleId) ?>" readonly>
                <?php } else { ?>
                    <select name="grant_role_id" class="form-select" required>
                        <option value="">選択してください</option>
                        <?php foreach ($grantRoleOptions as $option) { ?>
                            <option value="<?= h($option['value']) ?>"><?= h($option['label']) ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>
            </div>

            <?php if ($isEdit) { ?>
                <div class="mb-3">
                    <label class="form-label">権限</label>
                    <div class="row">
                        <?php foreach ($grantPermissionOptions as $option) { ?>
                            <div class="col-md-4">
                                <label>
                                    <input type="checkbox" name="grant_permission_ids[]" value="<?= h($option['value']) ?>" <?= in_array($option['value'], $selectedPermissionIds, true) ? 'checked' : '' ?>>
                                    <?= h($option['label']) ?>
                                </label>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } else { ?>
                <div class="mb-3">
                    <label class="form-label">権限</label>
                    <select name="grant_permission_id" class="form-select" required>
                        <option value="">選択してください</option>
                        <?php foreach ($grantPermissionOptions as $option) { ?>
                            <option value="<?= h($option['value']) ?>"><?= h($option['label']) ?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>

            <div class="text-center">
                <a href="<?= $this->Url->build(['controller' => 'RolePermission/Search', 'action' => 'index', 'account_id' => $this->getRequest()->getParam('account_id'), '?' => $this->getRequest()->getQuery()]) ?>" class="btn btn-secondary px-5">戻る</a>
                <button type="submit" class="btn btn-primary px-5"><?= $isEdit ? '更新' : '作成' ?></button>
            </div>
        </form>
    </div>
</div>
