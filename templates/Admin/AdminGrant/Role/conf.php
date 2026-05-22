<?php
/** @var \App\Service\Controller\Shared\Process\Process\InputProcess $input */
/** @var bool $isEdit */
?>
<div class="card shadow-sm">
    <div class="card-header bg-success text-white">
        <?= $isEdit ? 'ロール権限更新（入力内容確認）' : 'ロール権限新規作成（入力内容確認）' ?>
    </div>

    <div class="card-body">
        <form method="post">
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">
            <input type="hidden" name="_process_key" value="<?= h($input->getInput('_process_key')) ?>">

            <table class="table table-bordered mb-4">
                <?php if ($isEdit) { ?>
                <tr>
                    <th style="width: 30%">ID</th>
                    <td><?= h($input->getInput('grant_role_id')) ?></td>
                </tr>
                <?php } ?>
                <tr>
                    <th style="width: 30%">コード</th>
                    <td><?= h($input->getInput('code')) ?></td>
                </tr>
                <tr>
                    <th>名称</th>
                    <td><?= h($input->getInput('name')) ?></td>
                </tr>
                <tr>
                    <th>説明</th>
                    <td><?= h($input->getInput('description')) ?></td>
                </tr>
                <tr>
                    <th>並び順</th>
                    <td><?= h($input->getInput('sort')) ?></td>
                </tr>
                <tr>
                    <th>有効状態</th>
                    <td><?= (string)$input->getInput('is_active') === '1' ? '有効' : '無効' ?></td>
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
                    <?= $isEdit ? '更新する' : '作成する' ?>
                </button>
            </div>
        </form>
    </div>
</div>
