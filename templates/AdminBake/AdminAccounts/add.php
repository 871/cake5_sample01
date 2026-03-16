<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\AdminAccount $adminAccount
 * @var \Cake\Collection\CollectionInterface|string[] $accountStatusMasters
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Admin Accounts'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="adminAccounts form content">
            <?= $this->Form->create($adminAccount) ?>
            <fieldset>
                <legend><?= __('Add Admin Account') ?></legend>
                <?php
                    echo $this->Form->control('email');
                    echo $this->Form->control('password');
                    echo $this->Form->control('name');
                    echo $this->Form->control('admin_note');
                    echo $this->Form->control('account_status_master_id', ['options' => $accountStatusMasters]);
                    echo $this->Form->control('is_email_verified');
                    echo $this->Form->control('password_changed_at');
                    echo $this->Form->control('password_expires_at');
                    echo $this->Form->control('created_by');
                    echo $this->Form->control('created_ip');
                    echo $this->Form->control('modified_by');
                    echo $this->Form->control('modified_ip');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
