<?php
/* @var \App\Service\Controller\Shared\Process\Process\InputProcess $input */
/* @var array $accountStatusOptions */

$statusLabel = '';
foreach ($accountStatusOptions as $option) {
    if ($option['value'] === $input->getInput('account_status_master_id')) {
        $statusLabel = $option['label'];
        break;
    }
}
?>
<div class="card shadow-sm">
    <div class="card-header bg-success text-white">
        <?= $input->getInput('id') ? h('更新') : h('新規登録') ?>
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
                    <th style="width:30%">メールアドレス</th>
                    <td><?= h($input->getInput('email')) ?></td>
                </tr>
                <tr>
                    <th>パスワード</th>
                    <td><?= $input->getInput('password') !== '' ? '（変更あり）' : ($input->getInput('id') ? '（変更なし）' : '（入力済み）') ?></td>
                </tr>
                <tr>
                    <th>名前</th>
                    <td><?= h($input->getInput('name')) ?></td>
                </tr>
                <tr>
                    <th>管理者メモ</th>
                    <td style="white-space:pre-wrap"><?= h($input->getInput('admin_note')) ?></td>
                </tr>
            </table>

            <h6 class="border-bottom pb-2 mb-3">ステータス・権限</h6>
            <table class="table table-bordered">
                <tr>
                    <th style="width:30%">アカウントステータス</th>
                    <td><?= h($statusLabel ?: $input->getInput('account_status_master_id')) ?></td>
                </tr>
                <tr>
                    <th>メール確認</th>
                    <td><?= $input->getInput('is_email_verified') === '1' ? '確認済み' : '未確認' ?></td>
                </tr>
            </table>

            <h6 class="border-bottom pb-2 mb-3">パスワード管理</h6>
            <table class="table table-bordered">
                <tr>
                    <th style="width:30%">パスワード変更日時</th>
                    <td><?= h($input->getInput('password_changed_at')) ?></td>
                </tr>
                <tr>
                    <th>パスワード有効期限</th>
                    <td><?= h($input->getInput('password_expires_at')) ?></td>
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
                    登録する
                </button>
            </div>
        </form>
    </div>
</div>
