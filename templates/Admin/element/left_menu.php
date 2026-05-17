<?php
$request = $this->getRequest();
$accountId = $request->getParam('account_id');
$prefix = (string)$request->getParam('prefix');


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
            <details class="admin-menu-group"<?= in_array((string)$request->getParam('prefix'), [
                'Admin/Log/LoginLog',
                'Admin/Log/PageAccessLog',
            ], true) ? ' open' : '' ?>>
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
            <details class="admin-menu-group"<?= in_array((string)$request->getParam('prefix'), [
                'Admin/MailManage',
                'Admin/AdminAccount',
                'Admin/AdminGrant',
            ], true) ? ' open' : '' ?>>
                <summary class="nav-link text-white">システム管理</summary>
                <ul class="nav flex-column admin-submenu">
                    <li class="nav-item">
                        <a
                            class="nav-link text-white"
                            href="<?= $this->Url->build([
                                'prefix' => 'Admin/MailManage',
                                'controller' => 'Search',
                                'action' => 'init',
                                'account_id' => $accountId,
                            ]) ?>"
                        >システムメール</a>
                    </li>
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
                    <li class="nav-item">
                        <a
                            class="nav-link text-white"
                            href="<?= $this->Url->build([
                                'prefix' => 'Admin/AdminGrant',
                                'controller' => 'RolePermission/Search',
                                'action' => 'init',
                                'account_id' => $accountId,
                            ]) ?>"
                        >管理者権限</a>
                    </li>
                </ul>
            </details>
        </li>
    </ul>
</aside>
