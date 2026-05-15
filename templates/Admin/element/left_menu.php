<?php
$request = $this->getRequest();
$accountId = $request->getParam('account_id');
$prefix = (string)$request->getParam('prefix');
$isLogMenuOpen = in_array($prefix, ['Admin/Log/LoginLog', 'Admin/Log/PageAccessLog'], true);
$isSystemMenuOpen = $prefix === 'Admin/AdminAccount';
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
                    'account_id' => $accountId,
                ]) ?>"
            >Top</a>
        </li>
        <li class="nav-item">
            <details class="admin-menu-group"<?= $isLogMenuOpen ? ' open' : '' ?>>
                <summary class="nav-link text-white">ログ管理</summary>
                <ul class="nav flex-column admin-submenu">
                    <li class="nav-item">
                        <a
                            class="nav-link text-white"
                            href="<?= $this->Url->build([
                                'prefix' => 'Admin/Log/LoginLog',
                                'controller' => 'Search',
                                'action' => 'init',
                                'account_id' => $accountId,
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
                                'account_id' => $accountId,
                            ]) ?>"
                        >ページアクセスログ</a>
                    </li>
                </ul>
            </details>
        </li>
        <li class="nav-item">
            <details class="admin-menu-group"<?= $isSystemMenuOpen ? ' open' : '' ?>>
                <summary class="nav-link text-white">システム管理</summary>
                <ul class="nav flex-column admin-submenu">
                    <li class="nav-item">
                        <a
                            class="nav-link text-white"
                            href="<?= $this->Url->build([
                                'prefix' => 'Admin/AdminAccount',
                                'controller' => 'Search',
                                'action' => 'init',
                                'account_id' => $accountId,
                            ]) ?>"
                        >管理者アカウント</a>
                    </li>
                </ul>
            </details>
        </li>
    </ul>
</aside>
