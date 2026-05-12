<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>メール管理 管理画面</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- noUiSlider CSS -->
<link href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css" rel="stylesheet">

<link href="/v1/ad/css/lyaout.css" rel="stylesheet">
<link href="/v1/ad/css/search.css" rel="stylesheet">

<!-- jQuery（Select2は必要） -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- noUiSlider JS -->
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>
</head>
<body>

<header>
    <h5 class="mb-0">管理画面</h5>
</header>

<div class="wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
        <h6>メニュー</h6>
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
                >Dashboard</a>
            </li>
            <li class="nav-item">
                <a
                    class="nav-link text-white"
                    href="<?= $this->Url->build([
                        'prefix' => 'Admin/MailManage',
                        'controller' => 'Search',
                        'action' => 'init',
                        'account_id' => $this->getRequest()->getParam('account_id'),
                    ]) ?>"
                >メール情報</a>
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
                            'prefix' => 'Admin/MailManage',
                            'controller' => 'Search',
                            'action' => 'index',
                            'account_id' => $this->getRequest()->getParam('account_id'),
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>" class="nav-link">一覧</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $this->Url->build([
                            'prefix' => 'Admin/MailManage',
                            'controller' => 'MailTask',
                            'action' => 'sendWaitingMails',
                            'account_id' => $this->getRequest()->getParam('account_id'),
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>" class="nav-link">メール送信</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $this->Url->build([
                            'prefix' => 'Admin/MailManage',
                            'controller' => 'MailTask',
                            'action' => 'checkReceivedMails',
                            'account_id' => $this->getRequest()->getParam('account_id'),
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>" class="nav-link">受信確認</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $this->Url->build([
                            'prefix' => 'Admin/MailManage',
                            'controller' => 'MailTask',
                            'action' => 'checkBouncedMails',
                            'account_id' => $this->getRequest()->getParam('account_id'),
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>" class="nav-link">バウンス確認</a>
                    </li>
                    <?php if ($this->getRequest()->getParam('mail_id')): ?>
                    <li class="nav-item">
                        <a href="<?= $this->Url->build([
                            'prefix' => 'Admin/MailManage',
                            'controller' => 'Detail',
                            'action' => 'index',
                            'account_id' => $this->getRequest()->getParam('account_id'),
                            'mail_id' => $this->getRequest()->getParam('mail_id'),
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>" class="nav-link">詳細</a>
                    </li>
                    <?php endif ?>
                </ul>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <?= $this->Form->postLink('ログアウト', [
                            'prefix' => 'Admin',
                            'controller' => 'Logout',
                            'action' => 'index',
                            'account_id' => $this->getRequest()->getParam('account_id'),
                        ], [
                            'class' => 'nav-link',
                            'confirm' => 'ログアウトしますか？',
                        ]) ?>
                    </li>
                </ul>
            </div>
        </nav>
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
                    <b>メール情報</b>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= $this->Url->build([
                        'prefix' => 'Admin/MailManage',
                        'controller' => 'Create',
                        'action' => 'index',
                        'account_id' => $this->getRequest()->getParam('account_id'),
                        '?' => $this->getRequest()->getQuery(),
                    ]) ?>">作成</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= $this->Url->build([
                        'prefix' => 'Admin/MailManage',
                        'controller' => 'Search',
                        'action' => 'index',
                        'account_id' => $this->getRequest()->getParam('account_id'),
                        '?' => $this->getRequest()->getQuery(),
                    ]) ?>">検索</a>
                </li>

                <li class="breadcrumb-item">
                    <a href="<?= $this->Url->build([
                        'prefix' => 'Admin/MailManage',
                        'controller' => 'CheckMailServer',
                        'action' => 'index',
                        'account_id' => $this->getRequest()->getParam('account_id'),
                    ]) ?>" target="_blank">設定確認</a>
                </li>
                <?php if ($this->getRequest()->getParam('mail_id')): ?>
                <li class="breadcrumb-item">
                    <a href="<?= $this->Url->build([
                        'prefix' => 'Admin/MailManage',
                        'controller' => 'Detail',
                        'action' => 'index',
                        'account_id' => $this->getRequest()->getParam('account_id'),
                        'mail_id' => $this->getRequest()->getParam('mail_id'),
                        '?' => $this->getRequest()->getQuery(),
                    ]) ?>">詳細</a>
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
    © 2026 Admin System
</footer>

</body>
</html>
