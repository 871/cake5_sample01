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
            <b>ユーザアカウント</b>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/UserAccount',
                'controller' => 'Create',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">新規作成</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/UserAccount',
                'controller' => 'Search',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">検索</a>
        </li>
    <?php if ($this->getRequest()->getParam('user_account_id')): ?>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/UserAccount',
                'controller' => 'Detail',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'user_account_id' => $this->getRequest()->getParam('user_account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">詳細</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/UserAccount',
                'controller' => 'Edit',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'user_account_id' => $this->getRequest()->getParam('user_account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">更新</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/UserAccount',
                'controller' => 'Create',
                'action' => 'copy',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'user_account_id' => $this->getRequest()->getParam('user_account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">複製</a>
        </li>
        <li class="breadcrumb-item">
            <?= $this->Form->postLink('削除', [
                'prefix' => 'Admin/UserAccount',
                'controller' => 'Delete',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'user_account_id' => $this->getRequest()->getParam('user_account_id'),
                '?' => $this->getRequest()->getQuery(),
            ], [
                'confirm' => '削除しますか？'
            ]) ?>
        </li>
    <?php endif ?>
    </ol>
</nav>