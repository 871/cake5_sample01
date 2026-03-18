<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\AdminAccount> $adminAccounts
 */
?>
<div class="adminAccounts index content">
    <?= $this->Html->link(__('New Admin Account'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Admin Accounts') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('email') ?></th>
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th><?= $this->Paginator->sort('account_status_master_id') ?></th>
                    <th><?= $this->Paginator->sort('is_email_verified') ?></th>
                    <th><?= $this->Paginator->sort('password_changed_at') ?></th>
                    <th><?= $this->Paginator->sort('password_expires_at') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('created_by') ?></th>
                    <th><?= $this->Paginator->sort('created_ip') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th><?= $this->Paginator->sort('modified_by') ?></th>
                    <th><?= $this->Paginator->sort('modified_ip') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($adminAccounts as $adminAccount): ?>
                <tr>
                    <td><?= h($adminAccount->id) ?></td>
                    <td><?= h($adminAccount->email) ?></td>
                    <td><?= h($adminAccount->name) ?></td>
                    <td><?= $adminAccount->hasValue('account_status_master') ? $adminAccount->account_status_master->name : '' ?></td>
                    <td><?= $this->Number->format($adminAccount->is_email_verified) ?></td>
                    <td><?= h($adminAccount->password_changed_at) ?></td>
                    <td><?= h($adminAccount->password_expires_at) ?></td>
                    <td><?= h($adminAccount->created) ?></td>
                    <td><?= $adminAccount->created_by === null ? '' : $this->Number->format($adminAccount->created_by) ?></td>
                    <td><?= h($adminAccount->created_ip) ?></td>
                    <td><?= h($adminAccount->modified) ?></td>
                    <td><?= $adminAccount->modified_by === null ? '' : $this->Number->format($adminAccount->modified_by) ?></td>
                    <td><?= h($adminAccount->modified_ip) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $adminAccount->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $adminAccount->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $adminAccount->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $adminAccount->id),
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>