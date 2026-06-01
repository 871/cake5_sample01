<?php
/** @var \App\Application\Controller\Shared\Process\Process\InputProcess $input */
/** @var bool $isEdit */
/** @var array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission> $grantPermissionOptions */

$selectedGrantPermissionIds = (array)$input->getInput('grant_permission_ids');
?>
<div class="card shadow-sm">
<?php foreach ($input->getInput('_errorMessages') as $message) { ?>
    <div class="alert alert-error-custom" onclick="this.style.display='none'">
        <?= h($message) ?>
    </div>
<?php } ?>
    <div class="card-header bg-primary text-white">
        <?= $isEdit ? 'ロール権限更新' : 'ロール権限新規作成' ?>
    </div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">
            <input type="hidden" name="_process_key" value="<?= h($input->getInput('_process_key')) ?>">
            <?php if ($isEdit) { ?>
                <input type="hidden" name="grant_role_id" value="<?= h($input->getInput('grant_role_id')) ?>">
            <?php } ?>

            <div class="row g-3">
                <?php if ($isEdit) { ?>
                <div class="col-md-2">
                    <label class="form-label">ID</label>
                    <input type="text" class="form-control" value="<?= h($input->getInput('grant_role_id')) ?>" disabled>
                </div>
                <?php } ?>
                <div class="col-md-4">
                    <label class="form-label">コード</label>
                    <input type="text" name="code" class="form-control <?= h($input->getInput('_errorFields.code')) ?>" value="<?= h($input->getInput('code')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">名称</label>
                    <input type="text" name="name" class="form-control <?= h($input->getInput('_errorFields.name')) ?>" value="<?= h($input->getInput('name')) ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">並び順</label>
                    <input type="number" name="sort" class="form-control <?= h($input->getInput('_errorFields.sort')) ?>" value="<?= h($input->getInput('sort')) ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">有効状態</label>
                    <select name="is_active" class="form-select <?= h($input->getInput('_errorFields.is_active')) ?>">
                        <option value="1" <?= (string)$input->getInput('is_active') === '1' ? 'selected' : '' ?>>有効</option>
                        <option value="0" <?= (string)$input->getInput('is_active') === '0' ? 'selected' : '' ?>>無効</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">説明</label>
                    <input type="text" name="description" class="form-control <?= h($input->getInput('_errorFields.description')) ?>" value="<?= h($input->getInput('description')) ?>">
                </div>
            </div>

            <div class="mt-4">
                <label class="form-label">権限設定</label>
                <div class="<?= h($input->getInput('_errorFields.grant_permission_ids')) ?>">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 100px;">権限ID</th>
                                <th>権限コード</th>
                                <th>権限名</th>
                                <th>説明</th>
                                <th style="width: 150px;">設定</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grantPermissionOptions as $option) { ?>
                                <?php $isGranted = in_array($option->grantPermissionId()->toString(), $selectedGrantPermissionIds, true); ?>
                                <tr>
                                    <td><?= h($option->grantPermissionId()->toString()) ?></td>
                                    <td><?= h($option->code()->toString()) ?></td>
                                    <td><?= h($option->name()->toString()) ?></td>
                                    <td><?= h($option->description()->toString()) ?></td>
                                    <td>
                                        <label class="form-check-label me-3">
                                            <input
                                                type="radio"
                                                class="form-check-input"
                                                name="grant_permission_ids[<?= h($option->grantPermissionId()->toString()) ?>]"
                                                value="1"
                                                <?= $isGranted ? 'checked' : '' ?>
                                            >あり
                                        </label>
                                        <label class="form-check-label">
                                            <input
                                                type="radio"
                                                class="form-check-input"
                                                name="grant_permission_ids[<?= h($option->grantPermissionId()->toString()) ?>]"
                                                value="0"
                                                <?= !$isGranted ? 'checked' : '' ?>
                                            >なし
                                        </label>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-5">確認へ</button>
            </div>
        </form>
    </div>
</div>
