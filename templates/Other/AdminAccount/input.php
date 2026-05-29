<?php
/* @var \App\Application\Controller\Shared\Process\Process\InputProcess $input */
/* @var array $accountStatusOptions */
?>
<!-- フォームカード -->
<div class="card shadow-sm">
<?php foreach ($input->getInput('_errorMessages') as $message) { ?>
    <div class="alert alert-error-custom" onclick="this.style.display='none'">
        <?= h($message) ?>
    </div>
<?php } ?>
    <div class="card-header bg-primary text-white">
        <?= $input->getInput('id') ? h('更新') : h('新規登録') ?>
    </div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">
            <input type="hidden" name="_process_key" value="<?= h($input->getInput('_process_key')) ?>">

            <h6 class="border-bottom pb-2 mb-3">ID</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <input
                        type="text"
                        class="form-control"
                        value="<?= h($input->getInput('id', '（新規作成）')) ?>"
                        readonly
                    >
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3">アカウント情報</h6>
            <div class="row mb-3">
                <div class="col-md-12 mb-2">
                    <label class="form-label">メールアドレス <span class="text-danger">*</span></label>
                    <input
                        type="email"
                        name="email"
                        class="form-control <?= h($input->getInput('_errorFields.email')) ?>"
                        value="<?= h($input->getInput('email')) ?>"
                        maxlength="255"
                        required
                    >
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label">
                        パスワード <?= $input->getInput('id') ? '' : '<span class="text-danger">*</span>' ?>
                    </label>
                    <?php if ($input->getInput('id')) { ?>
                        <div class="form-text text-muted mb-1">空白の場合は現在のパスワードを維持します。</div>
                    <?php } ?>
                    <input
                        type="password"
                        name="password"
                        class="form-control <?= h($input->getInput('_errorFields.password')) ?>"
                        autocomplete="new-password"
                    >
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label">名前 <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="name"
                        class="form-control <?= h($input->getInput('_errorFields.name')) ?>"
                        value="<?= h($input->getInput('name')) ?>"
                        maxlength="100"
                        required
                    >
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label">管理者メモ</label>
                    <textarea
                        name="admin_note"
                        class="form-control <?= h($input->getInput('_errorFields.admin_note')) ?>"
                        rows="3"
                    ><?= h($input->getInput('admin_note')) ?></textarea>
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3">ステータス・権限</h6>
            <div class="row mb-3">
                <div class="col-md-6 mb-2">
                    <label class="form-label">アカウントステータス <span class="text-danger">*</span></label>
                    <select
                        name="account_status_master_id"
                        class="form-select <?= h($input->getInput('_errorFields.account_status_master_id')) ?>"
                        required
                    >
                        <option value="">選択してください</option>
                        <?php foreach ($accountStatusOptions as $option) { ?>
                            <option value="<?= h($option['value']) ?>"
                                <?= $input->getInput('account_status_master_id') === $option['value'] ? 'selected' : '' ?>>
                                <?= h($option['label']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">メール確認</label>
                    <div class="form-check mt-2">
                        <input
                            type="hidden"
                            name="is_email_verified"
                            value="0"
                        >
                        <input
                            type="checkbox"
                            name="is_email_verified"
                            class="form-check-input"
                            value="1"
                            id="is_email_verified"
                            <?= $input->getInput('is_email_verified') === '1' ? 'checked' : '' ?>
                        >
                        <label class="form-check-label" for="is_email_verified">確認済み</label>
                    </div>
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3">パスワード管理</h6>
            <div class="row mb-3">
                <div class="col-md-6 mb-2">
                    <label class="form-label">パスワード変更日時 <span class="text-danger">*</span></label>
                    <input
                        type="datetime-local"
                        name="password_changed_at"
                        class="form-control <?= h($input->getInput('_errorFields.password_changed_at')) ?>"
                        value="<?= h($input->getInput('password_changed_at')) ?>"
                        step="1"
                        required
                    >
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">パスワード有効期限 <span class="text-danger">*</span></label>
                    <input
                        type="datetime-local"
                        name="password_expires_at"
                        class="form-control <?= h($input->getInput('_errorFields.password_expires_at')) ?>"
                        value="<?= h($input->getInput('password_expires_at')) ?>"
                        step="1"
                        required
                    >
                </div>
            </div>

            <!-- ボタン -->
            <div class="text-center mt-4">
                <a href="<?= $this->Url->build([
                    'controller' => 'Search',
                    'action' => 'index',
                    '?' => $this->getRequest()->getQuery(),
                ]) ?>" class="btn btn-secondary px-5">戻る</a>
                <button type="submit" class="btn btn-primary px-5 me-3">確認へ</button>
            </div>
        </form>
    </div>
</div>
