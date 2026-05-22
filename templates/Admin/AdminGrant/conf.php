<?php
/** @var \App\Service\Controller\Shared\Process\Process\InputProcess $input */
/** @var array<\App\Domain\Admin\AdminGrant\Entity\GrantRole> $grantRoleOptions */
/** @var array<\App\Domain\Admin\AdminGrant\Entity\GrantPermission> $grantPermissionOptions */

$selectedGrantRoleNames = array_values(array_map(
    fn($option) => $option->name()->toString(),
    array_filter(
        $grantRoleOptions,
        fn($option) => in_array($option->grantRoleId()->toString(), (array)$input->getInput('grant_role_ids'), true),
    ),
));

$selectedGrantPermissionNames = array_values(array_map(
    fn($option) => $option->name()->toString(),
    array_filter(
        $grantPermissionOptions,
        fn($option) => in_array($option->grantPermissionId()->toString(), (array)$input->getInput('grant_permission_ids'), true),
    ),
));
?>
<div class="card shadow-sm">
    <div class="card-header bg-success text-white">
        管理者権限更新（入力内容確認）
    </div>

    <div class="card-body">
        <form method="post">
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">
            <input type="hidden" name="_process_key" value="<?= h($input->getInput('_process_key')) ?>">

            <table class="table table-bordered mb-4">
                <tr>
                    <th style="width: 30%">管理者ID</th>
                    <td><?= h($input->getInput('admin_account_id')) ?></td>
                </tr>
                <tr>
                    <th>管理者名</th>
                    <td><?= h($input->getInput('name')) ?></td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td><?= h($input->getInput('email')) ?></td>
                </tr>
                <tr>
                    <th>権限ロール</th>
                    <td style="white-space: pre-wrap"><?= h(implode("\n", $selectedGrantRoleNames)) ?></td>
                </tr>
                <tr>
                    <th>アカウント権限</th>
                    <td style="white-space: pre-wrap"><?= h(implode("\n", $selectedGrantPermissionNames)) ?></td>
                </tr>
            </table>

            <div class="text-center mt-4">
                <a href="<?= $this->Url->build([
                    'action' => 'input',
                    'account_id' => $this->getRequest()->getParam('account_id'),
                    'process_id' => $this->getRequest()->getParam('process_id'),
                    '?' => $this->getRequest()->getQuery(),
                ]) ?>" class="btn btn-secondary px-5">修正する</a>
                <button type="submit" name="_process_action" value="complete" class="btn btn-success px-5 me-3">
                    更新する
                </button>
            </div>
        </form>
    </div>
</div>
