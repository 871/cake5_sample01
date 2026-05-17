<?php
/* @var \App\Service\Controller\Shared\Process\Process\InputProcess $input */
/* @var array $grantPermissionOptions */

$selectedLabels = [];
foreach ($grantPermissionOptions as $option) {
    if (in_array($option['value'], (array)$input->getInput('grant_permission_ids'), true)) {
        $selectedLabels[] = $option['label'];
    }
}
?>
<div class="card shadow-sm">
    <div class="card-header bg-success text-white">ロール権限更新（入力内容確認）</div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">
            <input type="hidden" name="_process_key" value="<?= h($input->getInput('_process_key')) ?>">

            <table class="table table-bordered">
                <tr>
                    <th style="width:30%">ロール</th>
                    <td><?= h($input->getInput('grant_role_id')) ?></td>
                </tr>
                <tr>
                    <th>権限</th>
                    <td><?= h($selectedLabels === [] ? '（なし）' : implode(', ', $selectedLabels)) ?></td>
                </tr>
            </table>

            <div class="text-center mt-4">
                <a href="<?= $this->Url->build(['action' => 'input', 'account_id' => $this->getRequest()->getParam('account_id'), 'process_id' => $this->getRequest()->getParam('process_id'), '?' => $this->getRequest()->getQuery()]) ?>" class="btn btn-secondary px-5">修正する</a>
                <button type="submit" class="btn btn-success px-5 me-3">更新する</button>
            </div>
        </form>
    </div>
</div>
