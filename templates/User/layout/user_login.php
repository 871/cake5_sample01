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
</header>

<div class="wrapper">
    <main class="main-content">
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
