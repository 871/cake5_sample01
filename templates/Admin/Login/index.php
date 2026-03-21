<div class="main-content d-flex align-items-center justify-content-center" style="min-height:70vh;">

    <div class="card shadow" style="width:100%; max-width:420px;">

        <div class="card-header bg-dark text-white text-center">
            管理画面ログイン
        </div>

        <div class="card-body p-4">

            <?= $this->Flash->render() ?>

            <?= $this->Form->create(null, ['url' => ['action' => 'indexPost']]) ?>

                <!-- メールアドレス -->
                <div class="mb-3">
                    <label class="form-label">メールアドレス</label>
                    <input type="email"
                        name="email"
                        class="form-control"
                        placeholder="example@example.com"
                        required>
                </div>

                <!-- パスワード -->
                <div class="mb-3">
                    <label class="form-label">パスワード</label>
                    <input type="password"
                        name="password"
                        class="form-control"
                        required>
                </div>

                <!-- ボタン -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">
                        ログイン
                    </button>
                </div>

            <?= $this->Form->end() ?>

        </div>
    </div>
</div>
