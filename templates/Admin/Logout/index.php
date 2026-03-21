<div class="main-content d-flex align-items-center justify-content-center" style="min-height:70vh;">

    <div class="card shadow" style="width:100%; max-width:420px;">

        <div class="card-header bg-dark text-white text-center">
            ログアウト
        </div>

        <div class="card-body p-4 text-center">
            <p>ログアウトしました。</p>
            <a href="<?= $this->Url->build(['prefix' => 'Admin', 'controller' => 'Login', 'action' => 'index']) ?>"
                class="btn btn-primary">
                ログイン画面へ
            </a>
        </div>

    </div>

</div>
