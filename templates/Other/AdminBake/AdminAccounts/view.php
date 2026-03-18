<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\AdminAccount $adminAccount
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Admin Account'), ['action' => 'edit', $adminAccount->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Admin Account'), ['action' => 'delete', $adminAccount->id], ['confirm' => __('Are you sure you want to delete # {0}?', $adminAccount->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Admin Accounts'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Admin Account'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="adminAccounts view content">
            <h3><?= h($adminAccount->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($adminAccount->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Email') ?></th>
                    <td><?= h($adminAccount->email) ?></td>
                </tr>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($adminAccount->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Account Status Master') ?></th>
                    <td><?= $adminAccount->hasValue('account_status_master') ? $adminAccount->account_status_master->name : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Created Ip') ?></th>
                    <td><?= h($adminAccount->created_ip) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified Ip') ?></th>
                    <td><?= h($adminAccount->modified_ip) ?></td>
                </tr>
                <tr>
                    <th><?= __('Is Email Verified') ?></th>
                    <td><?= $this->Number->format($adminAccount->is_email_verified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created By') ?></th>
                    <td><?= $adminAccount->created_by === null ? '' : $this->Number->format($adminAccount->created_by) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified By') ?></th>
                    <td><?= $adminAccount->modified_by === null ? '' : $this->Number->format($adminAccount->modified_by) ?></td>
                </tr>
                <tr>
                    <th><?= __('Password Changed At') ?></th>
                    <td><?= h($adminAccount->password_changed_at) ?></td>
                </tr>
                <tr>
                    <th><?= __('Password Expires At') ?></th>
                    <td><?= h($adminAccount->password_expires_at) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($adminAccount->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($adminAccount->modified) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Admin Note') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($adminAccount->admin_note)); ?>
                </blockquote>
            </div>
            <div class="related">
                <h4><?= __('Related Admin Account Histories') ?></h4>
                <?php if (!empty($adminAccount->admin_account_histories)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Email') ?></th>
                            <th><?= __('Password') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Admin Note') ?></th>
                            <th><?= __('Account Status Master Id') ?></th>
                            <th><?= __('Is Email Verified') ?></th>
                            <th><?= __('Password Changed At') ?></th>
                            <th><?= __('Password Expires At') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Created By') ?></th>
                            <th><?= __('Created Ip') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th><?= __('Modified By') ?></th>
                            <th><?= __('Modified Ip') ?></th>
                            <th><?= __('Operation Type') ?></th>
                            <th><?= __('History Created') ?></th>
                        </tr>
                        <?php foreach ($adminAccount->admin_account_histories as $adminAccountHistory) : ?>
                        <tr>
                            <td><?= h($adminAccountHistory->id) ?></td>
                            <td><?= h($adminAccountHistory->email) ?></td>
                            <td><?= h($adminAccountHistory->password) ?></td>
                            <td><?= h($adminAccountHistory->name) ?></td>
                            <td><?= h($adminAccountHistory->admin_note) ?></td>
                            <td><?= h($adminAccountHistory->account_status_master_id) ?></td>
                            <td><?= h($adminAccountHistory->is_email_verified) ?></td>
                            <td><?= h($adminAccountHistory->password_changed_at) ?></td>
                            <td><?= h($adminAccountHistory->password_expires_at) ?></td>
                            <td><?= h($adminAccountHistory->created) ?></td>
                            <td><?= h($adminAccountHistory->created_by) ?></td>
                            <td><?= h($adminAccountHistory->created_ip) ?></td>
                            <td><?= h($adminAccountHistory->modified) ?></td>
                            <td><?= h($adminAccountHistory->modified_by) ?></td>
                            <td><?= h($adminAccountHistory->modified_ip) ?></td>
                            <td><?= h($adminAccountHistory->operation_type) ?></td>
                            <td><?= h($adminAccountHistory->history_created) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>