<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>管理者アカウント管理</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<link href="/v1/ad/css/lyaout.css" rel="stylesheet">
<link href="/v1/ad/css/search.css" rel="stylesheet">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>

<header>
    <h5 class="mb-0">管理者アカウント管理</h5>
</header>

<div class="wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
        <h6>Menu</h6>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a 
                    class="nav-link text-white" 
                    href="<?= $this->Url->build([
                        'prefix' => 'AdminAccounts/Accounts',
                        'controller' => 'Create',
                        'action' => 'index',
                        '?' => $this->getRequest()->getQuery(),
                    ]) ?>"
                >新規作成</a>
            </li>
            <li class="nav-item">
                <a 
                    class="nav-link text-white" 
                    href="<?= $this->Url->build([
                        'prefix' => 'AdminAccounts/Accounts',
                        'controller' => 'Search',
                        'action' => 'init',
                    ]) ?>"
                >検索</a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header Menu -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-2 rounded">
            <div class="container-fluid px-2">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="<?= $this->Url->build([
                            'prefix' => 'AdminAccounts/Accounts',
                            'controller' => 'Create',
                            'action' => 'index',
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>" class="nav-link">新規作成</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $this->Url->build([
                            'prefix' => 'AdminAccounts/Accounts',
                            'controller' => 'Search',
                            'action' => 'index',
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>" class="nav-link">検索</a>
                    </li>
                <?php if ($this->getRequest()->getParam('admin_account_id')): ?>
                    <li class="nav-item">
                        <a href="<?= $this->Url->build([
                            'controller' => 'Detail',
                            'action' => 'index',
                            'admin_account_id' => $this->getRequest()->getParam('admin_account_id'),
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>" class="nav-link">詳細</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $this->Url->build([
                            'controller' => 'Edit',
                            'action' => 'index',
                            'admin_account_id' => $this->getRequest()->getParam('admin_account_id'),
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>" class="nav-link">更新</a>
                    </li>
                    <li class="nav-item">
                        <?= $this->Form->postLink('削除', [
                            'controller' => 'Delete',
                            'action' => 'index',
                            'admin_account_id' => $this->getRequest()->getParam('admin_account_id'),
                            '?' => $this->getRequest()->getQuery(),
                        ], [
                            'class' => 'nav-link',
                            'confirm' => '削除しますか？'
                        ]) ?>
                    </li>
                <?php endif ?>

                </ul>
            </div>
        </nav>
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item">
                    <a href="<?= $this->Url->build([
                        'prefix' => 'AdminAccounts/Accounts',
                        'controller' => 'Create',
                        'action' => 'index',
                        '?' => $this->getRequest()->getQuery(),
                    ]) ?>">新規作成</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= $this->Url->build([
                        'prefix' => 'AdminAccounts/Accounts',
                        'controller' => 'Search',
                        'action' => 'index',
                        '?' => $this->getRequest()->getQuery(),
                    ]) ?>">検索</a>
                </li>
            <?php if ($this->getRequest()->getParam('admin_account_id')): ?>
                <li class="breadcrumb-item">
                    <a href="<?= $this->Url->build([
                        'controller' => 'Detail',
                        'action' => 'index',
                        'admin_account_id' => $this->getRequest()->getParam('admin_account_id'),
                        '?' => $this->getRequest()->getQuery(),
                    ]) ?>">詳細</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= $this->Url->build([
                        'controller' => 'Edit',
                        'action' => 'index',
                        'admin_account_id' => $this->getRequest()->getParam('admin_account_id'),
                        '?' => $this->getRequest()->getQuery(),
                    ]) ?>">更新</a>
                </li>
                <li class="breadcrumb-item">
                    <?= $this->Form->postLink('削除', [
                        'controller' => 'Delete',
                        'action' => 'index',
                        'admin_account_id' => $this->getRequest()->getParam('admin_account_id'),
                        '?' => $this->getRequest()->getQuery(),
                    ], [
                        'confirm' => '削除しますか？'
                    ]) ?>
                </li>
            <?php endif ?>
            </ol>
        </nav>
        <!-- Message Area -->
        <div class="message-area mb-3">
            <?= $this->Flash->render() ?>
        </div>
        <div class="mb-3">
            <?= $this->fetch('content') ?>
        </div>
    </main>
</div>

<!-- Footer (Full Width) -->
<footer>
    © 2026 Admin Account Management System
</footer>

</body>
</html>
