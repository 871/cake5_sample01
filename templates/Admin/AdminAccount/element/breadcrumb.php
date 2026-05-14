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
            <b>管理者アカウント</b>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/AdminAccount',
                'controller' => 'Create',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">新規作成</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/AdminAccount',
                'controller' => 'Search',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">検索</a>
        </li>
    <?php if ($this->getRequest()->getParam('admin_account_id')): ?>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/AdminAccount',
                'controller' => 'Detail',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'admin_account_id' => $this->getRequest()->getParam('admin_account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">詳細</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/AdminAccount',
                'controller' => 'Edit',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'admin_account_id' => $this->getRequest()->getParam('admin_account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">更新</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/AdminAccount',
                'controller' => 'Create',
                'action' => 'copy',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'admin_account_id' => $this->getRequest()->getParam('admin_account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">複製</a>
        </li>
        <li class="breadcrumb-item">
            <?= $this->Form->postLink('削除', [
                'prefix' => 'Admin/AdminAccount',
                'controller' => 'Delete',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'admin_account_id' => $this->getRequest()->getParam('admin_account_id'),
                '?' => $this->getRequest()->getQuery(),
            ], [
                'confirm' => '削除しますか？'
            ]) ?>
        </li>
    <?php endif ?>
    </ol>
</nav>