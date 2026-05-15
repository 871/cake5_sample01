<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-3">
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin',
                'controller' => 'Top',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
            ]) ?>">Top</a>
        </li>
        <li class="breadcrumb-item">
            <b>ログイン試行ログ</b>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/Log/LoginLog',
                'controller' => 'Search',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">検索</a>
        </li>
    <?php if ($this->getRequest()->getParam('login_log_id')): ?>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/Log/LoginLog',
                'controller' => 'Detail',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'login_log_id' => $this->getRequest()->getParam('login_log_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">詳細</a>
        </li>
    <?php endif ?>
    </ol>
</nav>