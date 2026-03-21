<?php
/** @var \App\View\AppView $this */
?>
<div class="main-content d-flex align-items-center justify-content-center" style="min-height:70vh;">

    <div class="card shadow" style="width:100%; max-width:420px;">

        <div class="card-header bg-dark text-white text-center">
            管理画面ログイン
        </div>

        <div class="card-body p-4">

            <?= $this->Flash->render() ?>

            <?= $this->Form->create(null, [
                'url' => ['action' => 'indexPost'],
            ]) ?>

            <!-- メールアドレス -->
            <div class="mb-3">
                <label class="form-label">メールアドレス</label>
                <?= $this->Form->email('email', [
                    'class' => 'form-control',
                    'placeholder' => 'example@example.com',
                    'required' => true,
                ]) ?>
            </div>

            <!-- パスワード -->
            <div class="mb-3">
                <label class="form-label">パスワード</label>
                <?= $this->Form->password('password', [
                    'class' => 'form-control',
                    'required' => true,
                ]) ?>
            </div>

            <!-- ボタン -->
            <div class="d-grid mb-3">
                <?= $this->Form->button('ログイン', ['class' => 'btn btn-primary']) ?>
            </div>

            <!-- パスワード再設定 -->
            <div class="text-center">
                <a href="/password-reset" class="text-decoration-none small">
                    パスワードをお忘れですか？
                </a>
            </div>

            <?= $this->Form->end() ?>

        </div>
    </div>
</div>
