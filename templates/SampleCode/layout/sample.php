<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>MySQL Type Samples Search</title>
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
    <h5 class="mb-0">Sample Code</h5>
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
                        'prefix' => 'SampleCode/MySqlTypeSamples',
                        'controller' => 'Create',
                        'action' => 'index',
                        '?' => $this->getRequest()->getQuery(),
                    ]) ?>"
                >Create</a>
            </li>
            <li class="nav-item">
                <a 
                    class="nav-link text-white" 
                    href="<?= $this->Url->build([
                        'prefix' => 'SampleCode/MySqlTypeSamples',
                        'controller' => 'Search',
                        'action' => 'init',
                    ]) ?>"
                >Search</a>
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
                        <a 
                            class="nav-link" 
                            href="<?= $this->Url->build([
                                'prefix' => 'SampleCode/MySqlTypeSamples',
                                'controller' => 'Create',
                                'action' => 'index',
                                '?' => $this->getRequest()->getQuery(),
                            ]) ?>"
                        >新規作成</a>
                    </li>
                    <li class="nav-item">
                        <a 
                            class="nav-link" 
                            href="<?= $this->Url->build([
                                'prefix' => 'SampleCode/MySqlTypeSamples',
                                'controller' => 'Search',
                                'action' => 'index',
                                '?' => $this->getRequest()->getQuery(),
                            ]) ?>"
                        >検索</a>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item">
                    <a 
                        href="<?= $this->Url->build([
                            'prefix' => 'SampleCode/MySqlTypeSamples',
                            'controller' => 'Create',
                            'action' => 'index',
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>"
                    >新規作成</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <a 
                        href="<?= $this->Url->build([
                            'prefix' => 'SampleCode/MySqlTypeSamples',
                            'controller' => 'Search',
                            'action' => 'index',
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>"
                    >検索</a>
                </li>
            <?php if ($this->getRequest()->getParam('my_sql_type_sample_id')): ?>
                <a href="<?= $this->Url->build([
                    'controller' => 'Detail',
                    'action' => 'index',
                    'my_sql_type_sample_id' => $this->getRequest()->getParam('my_sql_type_sample_id'),
                    '?' => $this->getRequest()->getQuery(),
                ]) ?>" class="btn btn-sm btn-info me-2">詳細</a>
                <a href="<?= $this->Url->build([
                    'controller' => 'Edit',
                    'action' => 'index',
                    'my_sql_type_sample_id' => $this->getRequest()->getParam('my_sql_type_sample_id'),
                    '?' => $this->getRequest()->getQuery(),
                ]) ?>" class="btn btn-sm btn-info me-2">更新</a>
                <a href="<?= $this->Url->build([
                    'controller' => 'Create',
                    'action' => 'copy',
                    'my_sql_type_sample_id' => $this->getRequest()->getParam('my_sql_type_sample_id'),
                    '?' => $this->getRequest()->getQuery(),
                ]) ?>" class="btn btn-sm btn-info me-2">複製</a>
                <a href="<?= $this->Url->build([
                    'controller' => 'Delete',
                    'action' => 'indexPost',
                    'my_sql_type_sample_id' => $this->getRequest()->getParam('my_sql_type_sample_id'),
                    '?' => $this->getRequest()->getQuery(),
                ]) ?>" class="btn btn-sm btn-info me-2">削除</a>
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
    © 2026 MySQL Type Samples System
</footer>

</body>
</html>