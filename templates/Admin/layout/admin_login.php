<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>管理画面ログイン</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="/v1/ad/css/lyaout.css" rel="stylesheet">
</head>
<body>

<header>
    <h5 class="mb-0">MySQL Type Samples 管理画面</h5>
</header>

<div class="wrapper">

    <!-- Main Content -->
    <main class="main-content">
        <?= $this->fetch('content') ?>
    </main>
</div>

<!-- Footer (Full Width) -->
<footer>
    © 2026 MySQL Type Samples System
</footer>

</body>
</html>
