<?php
/* @var \App\Service\Controller\Shared\Process\Process\InputProcess $input */
?>
<div class="card shadow-sm">
<?php foreach ($input->getInput('_errorMessages') as $message) { ?>
    <div class="alert alert-error-custom" onclick="this.style.display='none'">
        <?= h($message) ?>
    </div>
<?php } ?>
    <div class="card-header bg-primary text-white">メール情報新規登録</div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">
            <input type="hidden" name="_process_key" value="<?= h($input->getInput('_process_key')) ?>">

            <div class="row mb-3">
                <div class="col-md-12 mb-2">
                    <label class="form-label">関連データキー <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="related_data_key"
                        class="form-control <?= h($input->getInput('_errorFields.related_data_key')) ?>"
                        value="<?= h($input->getInput('related_data_key')) ?>"
                        required
                    >
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">送信予定日時</label>
                    <input
                        type="datetime-local"
                        name="send_scheduled_at"
                        class="form-control <?= h($input->getInput('_errorFields.send_scheduled_at')) ?>"
                        value="<?= h($input->getInput('send_scheduled_at')) ?>"
                        step="1"
                    >
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label">タイトル <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="title"
                        class="form-control <?= h($input->getInput('_errorFields.title')) ?>"
                        value="<?= h($input->getInput('title')) ?>"
                        required
                    >
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label">本文 <span class="text-danger">*</span></label>
                    <textarea
                        name="body"
                        class="form-control <?= h($input->getInput('_errorFields.body')) ?>"
                        rows="5"
                        required
                    ><?= h($input->getInput('body')) ?></textarea>
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label">To <span class="text-danger">*</span></label>
                    <textarea
                        name="mail_to"
                        class="form-control <?= h($input->getInput('_errorFields.mail_to')) ?>"
                        rows="3"
                        required
                    ><?= h($input->getInput('mail_to')) ?></textarea>
                    <div class="form-text">複数指定する場合は改行で区切ってください。</div>
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label">Cc</label>
                    <textarea
                        name="mail_cc"
                        class="form-control <?= h($input->getInput('_errorFields.mail_cc')) ?>"
                        rows="3"
                    ><?= h($input->getInput('mail_cc')) ?></textarea>
                    <div class="form-text">複数指定する場合は改行で区切ってください。</div>
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label">Bcc</label>
                    <textarea
                        name="mail_bcc"
                        class="form-control <?= h($input->getInput('_errorFields.mail_bcc')) ?>"
                        rows="3"
                    ><?= h($input->getInput('mail_bcc')) ?></textarea>
                    <div class="form-text">複数指定する場合は改行で区切ってください。</div>
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label">受信確認アドレス <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="mail_received_check"
                        class="form-control <?= h($input->getInput('_errorFields.mail_received_check')) ?>"
                        value="<?= h($input->getInput('mail_received_check')) ?>"
                        required
                    >
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label">バウンス確認アドレス <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="mail_return_path"
                        class="form-control <?= h($input->getInput('_errorFields.mail_return_path')) ?>"
                        value="<?= h($input->getInput('mail_return_path')) ?>"
                        required
                    >
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-5 me-3">確認へ</button>
                <a href="<?= $this->Url->build([
                    'prefix' => 'Admin',
                    'controller' => 'Top',
                    'action' => 'index',
                    'account_id' => $this->getRequest()->getParam('account_id'),
                    '?' => $this->getRequest()->getQuery(),
                ]) ?>" class="btn btn-secondary px-5">戻る</a>
            </div>
        </form>
    </div>
</div>
