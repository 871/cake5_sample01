<?php

use App\Domain\Admin\AdminAccounts\ValueObject\Status;

/* @var \App\Service\Controller\Shared\Process\Process\InputProcess $input */
/* @var \App\Domain\Admin\AdminRoles\Entity\AdminRole[] $roles */

$roleOptions = [];
foreach ($roles as $role) {
    $roleOptions[$role->id()->toString()] = $role->name()->toString();
}

$isEdit = (bool)$input->getInput('id');
$roleName = $roleOptions[$input->getInput('role_id')] ?? h($input->getInput('role_id'));
$statusLabel = Status::LABELS[(int)$input->getInput('status')] ?? '';

?>
<div class="card shadow-sm">
    <div class="card-header bg-success text-white">
        <?= $isEdit ? h('更新') : h('新規登録') ?>
        （入力内容確認）
    </div>

    <div class="card-body">
        <form method="post">
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">
            <input type="hidden" name="_process_key" value="<?= h($input->getInput('_process_key')) ?>">

            <table class="table table-bordered">
                <tr>
                    <th style="width:30%">ID</th>
                    <td><?= h($input->getInput('id', '（新規作成）')) ?></td>
                </tr>
            </table>

            <h6 class="border-bottom pb-2 mb-3">アカウント情報</h6>
            <table class="table table-bordered">
                <tr>
                    <th style="width:30%">ログインID</th>
                    <td><?= h($input->getInput('login_id')) ?></td>
                </tr>
                <tr>
                    <th>名前</th>
                    <td><?= h($input->getInput('name')) ?></td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td><?= h($input->getInput('email')) ?></td>
                </tr>
                <tr>
                    <th>パスワード</th>
                    <td>
                        <?php if ($isEdit && $input->getInput('password') === ''): ?>
                            <span class="text-muted">（変更なし）</span>
                        <?php else: ?>
                            ●●●●●●●●
                        <?php endif ?>
                    </td>
                </tr>
            </table>

            <h6 class="border-bottom pb-2 mb-3">権限・ステータス</h6>
            <table class="table table-bordered">
                <tr>
                    <th style="width:30%">権限</th>
                    <td><?= h($roleName) ?></td>
                </tr>
                <tr>
                    <th>ステータス</th>
                    <td><?= h($statusLabel) ?></td>
                </tr>
            </table>

            <div class="text-center mt-4">
                <a href="<?= $this->Url->build([
                    'action' => 'input',
                    'process_id'=> $this->getRequest()->getParam('process_id'),
                    '?' => $this->getRequest()->getQuery(),
                ]) ?>" class="btn btn-secondary px-5">修正する</a>
                <button type="submit" name="_process_action" value="complete" class="btn btn-success px-5 ms-3">
                    登録する
                </button>
            </div>
        </form>
    </div>
</div>
