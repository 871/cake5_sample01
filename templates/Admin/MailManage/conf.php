<?php
/* @var \App\Service\Controller\Shared\Process\Process\InputProcess $input */
?>
<div class="card shadow-sm">
    <div class="card-header bg-success text-white">
        メール情報登録（入力内容確認）
    </div>

    <div class="card-body">
        <form method="post">
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">
            <input type="hidden" name="_process_key" value="<?= h($input->getInput('_process_key')) ?>">

            <table class="table table-bordered">
                <tr>
                    <th style="width:30%">関連データキー</th>
                    <td><?= h($input->getInput('related_data_key')) ?></td>
                </tr>
                <tr>
                    <th>送信予定日時</th>
                    <td><?= h($input->getInput('send_scheduled_at')) ?></td>
                </tr>
                <tr>
                    <th>タイトル</th>
                    <td><?= h($input->getInput('title')) ?></td>
                </tr>
                <tr>
                    <th>本文</th>
                    <td style="white-space:pre-wrap"><?= h($input->getInput('body')) ?></td>
                </tr>
                <tr>
                    <th>To</th>
                    <td style="white-space:pre-wrap"><?= h($input->getInput('mail_to')) ?></td>
                </tr>
                <tr>
                    <th>Cc</th>
                    <td style="white-space:pre-wrap"><?= h($input->getInput('mail_cc')) ?></td>
                </tr>
                <tr>
                    <th>Bcc</th>
                    <td style="white-space:pre-wrap"><?= h($input->getInput('mail_bcc')) ?></td>
                </tr>
                <tr>
                    <th>受信確認アドレス</th>
                    <td><?= h($input->getInput('mail_received_check')) ?></td>
                </tr>
                <tr>
                    <th>バウンス確認アドレス</th>
                    <td><?= h($input->getInput('mail_return_path')) ?></td>
                </tr>
            </table>

            <div class="text-center mt-4">
                <button type="submit" name="_process_action" value="complete" class="btn btn-success px-5 me-3">
                    登録する
                </button>
                <a href="<?= $this->Url->build([
                    'action' => 'input',
                    'account_id' => $this->getRequest()->getParam('account_id'),
                    'process_id' => $this->getRequest()->getParam('process_id'),
                    '?' => $this->getRequest()->getQuery(),
                ]) ?>" class="btn btn-secondary px-5">修正する</a>
            </div>
        </form>
    </div>
</div>
