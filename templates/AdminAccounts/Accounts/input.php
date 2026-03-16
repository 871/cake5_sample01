<?php

use App\Domain\Admin\AdminAccounts\ValueObject\Status;

/* @var \App\Service\Controller\Shared\Process\Process\InputProcess $input */
/* @var \App\Domain\Admin\AdminRoles\Entity\AdminRole[] $roles */

$roleOptions = [];
foreach ($roles as $role) {
    $roleOptions[$role->id()->toString()] = $role->name()->toString();
}

$isEdit = (bool)$input->getInput('id');

?>
<!-- Form Card -->
<div class="card shadow-sm">
<?php foreach ($input->getInput('_errorMessages') as $message) { ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <?= h($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>
    <div class="card-header bg-primary text-white">
        <?= $isEdit ? h('更新') : h('新規登録') ?>
    </div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">
            <input type="hidden" name="_process_key" value="<?= h($input->getInput('_process_key')) ?>">
            <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= h($input->getInput('id')) ?>">
            <?php endif ?>

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
                <div class="col-md-6">
                    <label class="form-label">ログインID <span class="badge bg-danger">必須</span></label>
                    <input 
                        type="text" 
                        name="login_id" 
                        class="form-control <?= $input->getInput('_errorFields.login_id') ? 'is-invalid' : '' ?>"
                        value="<?= h($input->getInput('login_id')) ?>"
                        maxlength="100"
                        placeholder="半角英数字・記号（_-.@）"
                    >
                    <?php if ($input->getInput('_errorFields.login_id')): ?>
                    <div class="invalid-feedback"><?= h($input->getInput('_errorFields.login_id')) ?></div>
                    <?php endif ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">名前 <span class="badge bg-danger">必須</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        class="form-control <?= $input->getInput('_errorFields.name') ? 'is-invalid' : '' ?>"
                        value="<?= h($input->getInput('name')) ?>"
                        maxlength="100"
                    >
                    <?php if ($input->getInput('_errorFields.name')): ?>
                    <div class="invalid-feedback"><?= h($input->getInput('_errorFields.name')) ?></div>
                    <?php endif ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">メールアドレス <span class="badge bg-danger">必須</span></label>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control <?= $input->getInput('_errorFields.email') ? 'is-invalid' : '' ?>"
                        value="<?= h($input->getInput('email')) ?>"
                        maxlength="255"
                    >
                    <?php if ($input->getInput('_errorFields.email')): ?>
                    <div class="invalid-feedback"><?= h($input->getInput('_errorFields.email')) ?></div>
                    <?php endif ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">
                        パスワード
                        <?= $isEdit ? '' : '<span class="badge bg-danger">必須</span>' ?>
                        <?= $isEdit ? '<small class="text-muted">（空白の場合は変更しない）</small>' : '' ?>
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        class="form-control <?= $input->getInput('_errorFields.password') ? 'is-invalid' : '' ?>"
                        placeholder="<?= $isEdit ? '変更する場合のみ入力' : '8文字以上' ?>"
                        autocomplete="new-password"
                    >
                    <?php if ($input->getInput('_errorFields.password')): ?>
                    <div class="invalid-feedback"><?= h($input->getInput('_errorFields.password')) ?></div>
                    <?php endif ?>
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3">権限・ステータス</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">権限 <span class="badge bg-danger">必須</span></label>
                    <select 
                        name="role_id" 
                        class="form-select <?= $input->getInput('_errorFields.role_id') ? 'is-invalid' : '' ?>"
                    >
                        <option value="">-- 選択してください --</option>
                        <?php foreach ($roleOptions as $id => $name): ?>
                            <option 
                                value="<?= h($id) ?>"
                                <?= $input->getInput('role_id') === $id ? 'selected' : '' ?>
                            ><?= h($name) ?></option>
                        <?php endforeach ?>
                    </select>
                    <?php if ($input->getInput('_errorFields.role_id')): ?>
                    <div class="invalid-feedback"><?= h($input->getInput('_errorFields.role_id')) ?></div>
                    <?php endif ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ステータス <span class="badge bg-danger">必須</span></label>
                    <select 
                        name="status" 
                        class="form-select <?= $input->getInput('_errorFields.status') ? 'is-invalid' : '' ?>"
                    >
                        <?php foreach (Status::LABELS as $value => $label): ?>
                            <option 
                                value="<?= h((string)$value) ?>"
                                <?= $input->getInput('status') === (string)$value ? 'selected' : '' ?>
                            ><?= h($label) ?></option>
                        <?php endforeach ?>
                    </select>
                    <?php if ($input->getInput('_errorFields.status')): ?>
                    <div class="invalid-feedback"><?= h($input->getInput('_errorFields.status')) ?></div>
                    <?php endif ?>
                </div>
            </div>

            <!-- ボタン -->
            <div class="text-center mt-4">
                <a href="<?= $this->Url->build([
                    'controller' => 'Search',
                    'action' => 'index',
                    '?' => $this->getRequest()->getQuery(),
                ]) ?>" class="btn btn-secondary px-5">戻る</a>
                <button type="submit" class="btn btn-primary px-5 ms-3">確認へ</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
