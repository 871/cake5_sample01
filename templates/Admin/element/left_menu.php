<?php



?>
<!-- Sidebar -->
<aside class="sidebar">
    <h6>管理者メニュー</h6>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a
                class="nav-link text-white"
                href="<?= $this->Url->build([
                    'prefix' => 'Admin',
                    'controller' => 'Top',
                    'action' => 'index',
                    'account_id' => $this->getRequest()->getParam('account_id'),
                ]) ?>"
            >Top</a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link text-white"
                href="<?= $this->Url->build([
                    'prefix' => 'Admin/Log/LoginLog',
                    'controller' => 'Search',
                    'action' => 'init',
                    'account_id' => $this->getRequest()->getParam('account_id'),
                ]) ?>"
            >ログイン試行ログ</a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link text-white"
                href="<?= $this->Url->build([
                    'prefix' => 'Admin/Log/PageAccessLog',
                    'controller' => 'Search',
                    'action' => 'init',
                    'account_id' => $this->getRequest()->getParam('account_id'),
                ]) ?>"
            >ページアクセスログ</a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link text-white"
                href="<?= $this->Url->build([
                    'prefix' => 'Admin/AdminAccount',
                    'controller' => 'Search',
                    'action' => 'init',
                    'account_id' => $this->getRequest()->getParam('account_id'),
                ]) ?>"
            >管理者アカウント</a>
        </li>
    </ul>
</aside>