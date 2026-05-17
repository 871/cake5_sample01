<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ユーザー画面</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/v1/ad/css/lyaout.css" rel="stylesheet">
</head>
<body>

<header>
    <h5 class="mb-0">ユーザー画面</h5>
    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
            <?= $this->Form->postLink('ログアウト', [
                'prefix' => 'User',
                'controller' => 'Logout',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
            ], [
                'class' => 'nav-link',
                'confirm' => 'ログアウトしますか？',
            ]) ?>
        </li>
    </ul>
</header>

<div class="wrapper">
    <main class="main-content">
        <div class="message-area mb-3">
            <?= $this->Flash->render() ?>
        </div>
        <div class="mb-3">
            <?= $this->fetch('content') ?>
        </div>
    </main>
</div>

<footer>
    © 2026 User System
</footer>

</body>
</html>
