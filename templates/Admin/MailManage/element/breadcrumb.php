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
            <b>システムメール</b>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/MailManage',
                'controller' => 'Create',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">新規登録</a>
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
        <li class="breadcrumb-item">
            <b>システム管理者機能</b>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/MailManage',
                'controller' => 'CheckMailServer',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
            ]) ?>" target="_blank">設定確認</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/MailManage',
                'controller' => 'MailTask',
                'action' => 'sendWaitingMails',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">メール送信</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/MailManage',
                'controller' => 'MailTask',
                'action' => 'checkReceivedMails',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">受信確認</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/MailManage',
                'controller' => 'MailTask',
                'action' => 'checkBouncedMails',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>">バウンス確認</a>
        </li>
    </ol>
</nav>