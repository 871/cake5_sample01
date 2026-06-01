<?php
/** @var \App\Application\Controller\Shared\Process\Process\InputProcess $input */
/** @var array<\App\Domain\Admin\AdminGrant\Entity\GrantRole> $grantRoleOptions */
/** @var array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission> $grantPermissionOptions */
?>
<div class="card shadow-sm">
<?php foreach ($input->getInput('_errorMessages') as $message) { ?>
    <div class="alert alert-error-custom" onclick="this.style.display='none'">
        <?= h($message) ?>
    </div>
<?php } ?>
    <div class="card-header bg-primary text-white">
        ユーザ権限更新
    </div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">
            <input type="hidden" name="_process_key" value="<?= h($input->getInput('_process_key')) ?>">

            <table class="table table-bordered mb-4">
                <tr>
                    <th style="width: 30%">ユーザID</th>
                    <td><?= h($input->getInput('user_account_id')) ?></td>
                </tr>
                <tr>
                    <th>ユーザ名</th>
                    <td><?= h($input->getInput('name')) ?></td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td><?= h($input->getInput('email')) ?></td>
                </tr>
            </table>

            <div class="row mb-3">
                <div class="col-md-6 mb-2">
                    <label class="form-label">権限ロール</label>
                    <select
                        name="grant_role_ids[]"
                        class="form-select <?= h($input->getInput('_errorFields.grant_role_ids')) ?>"
                        multiple
                        size="10"
                    >
                        <?php foreach ($grantRoleOptions as $option) { ?>
                            <option
                                value="<?= h($option->grantRoleId()->toString()) ?>"
                                <?= in_array($option->grantRoleId()->toString(), (array)$input->getInput('grant_role_ids'), true) ? 'selected' : '' ?>
                            >
                                <?= h($option->name()->toString()) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">アカウント権限</label>
                    <select
                        name="grant_permission_ids[]"
                        class="form-select <?= h($input->getInput('_errorFields.grant_permission_ids')) ?>"
                        multiple
                        size="10"
                    >
                        <?php foreach ($grantPermissionOptions as $option) { ?>
                            <option
                                value="<?= h($option->grantPermissionId()->toString()) ?>"
                                <?= in_array($option->grantPermissionId()->toString(), (array)$input->getInput('grant_permission_ids'), true) ? 'selected' : '' ?>
                            >
                                <?= h($option->name()->toString()) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-5 me-3">確認へ</button>
            </div>
        </form>
    </div>
</div>
