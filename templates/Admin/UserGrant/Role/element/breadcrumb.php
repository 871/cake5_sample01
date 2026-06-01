<?php
/** @var \App\Application\Controller\Shared\Process\Process\InputProcess $input */
?>
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
            <b>ユーザ権限</b>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/UserGrant',
                'controller' => 'Search',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">検索</a>
        </li>
        <li class="breadcrumb-item">
            <b>ロール権限</b>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/UserGrant/Role',
                'controller' => 'Create',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">新規作成</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/UserGrant/Role',
                'controller' => 'Search',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">検索</a>
        </li>
    <?php if ($this->getRequest()->getParam('grant_role_id')): ?>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/UserGrant/Role',
                'controller' => 'Detail',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'grant_role_id' => $this->getRequest()->getParam('grant_role_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">詳細</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/UserGrant/Role',
                'controller' => 'Edit',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'grant_role_id' => $this->getRequest()->getParam('grant_role_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">更新</a>
        </li>
    <?php endif ?>
    <?php if (
        $this->getRequest()->getParam('process_id')
        && isset($input)
        && !empty($input->getInput('grant_role_id'))
    ): ?>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/UserGrant/Role',
                'controller' => 'Detail',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'grant_role_id' => $input->getInput('grant_role_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">詳細</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/UserGrant/Role',
                'controller' => 'Edit',
                'action' => 'input',
                'account_id' => $this->getRequest()->getParam('account_id'),
                'process_id' => $this->getRequest()->getParam('process_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">更新</a>
        </li>
    <?php endif ?>
    </ol>
</nav>
