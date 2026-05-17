<?php
/* @var \App\Service\Controller\Shared\Process\Process\InputProcess $input */
/* @var array $grantRoleOptions */
/* @var array $grantPermissionOptions */
?>
<div class="card shadow-sm">
<?php foreach ($input->getInput('_errorMessages') as $message) { ?>
    <div class="alert alert-error-custom" onclick="this.style.display='none'">
        <?= h($message) ?>
    </div>
<?php } ?>
    <div class="card-header bg-primary text-white">ロール権限新規作成</div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">
            <input type="hidden" name="_process_key" value="<?= h($input->getInput('_process_key')) ?>">

            <div class="mb-3">
                <label class="form-label">ロール <span class="text-danger">*</span></label>
                <select name="grant_role_id" class="form-select <?= h($input->getInput('_errorFields.grant_role_id')) ?>" required>
                    <option value="">選択してください</option>
                    <?php foreach ($grantRoleOptions as $option) { ?>
                        <option value="<?= h($option['value']) ?>" <?= $input->getInput('grant_role_id') === $option['value'] ? 'selected' : '' ?>>
                            <?= h($option['label']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">権限 <span class="text-danger">*</span></label>
                <select name="grant_permission_id" class="form-select <?= h($input->getInput('_errorFields.grant_permission_id')) ?>" required>
                    <option value="">選択してください</option>
                    <?php foreach ($grantPermissionOptions as $option) { ?>
                        <option value="<?= h($option['value']) ?>" <?= $input->getInput('grant_permission_id') === $option['value'] ? 'selected' : '' ?>>
                            <?= h($option['label']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="text-center">
                <a href="<?= $this->Url->build(['controller' => 'RolePermission/Search', 'action' => 'index', 'account_id' => $this->getRequest()->getParam('account_id'), '?' => $this->getRequest()->getQuery()]) ?>" class="btn btn-secondary px-5">戻る</a>
                <button type="submit" class="btn btn-primary px-5">確認へ</button>
            </div>
        </form>
    </div>
</div>
