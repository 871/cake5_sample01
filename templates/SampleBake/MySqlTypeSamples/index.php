<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\MySqlTypeSample> $mySqlTypeSamples
 */
?>
<div class="mySqlTypeSamples index content">
    <?= $this->Html->link(__('New My Sql Type Sample'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('My Sql Type Samples') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('int_col') ?></th>
                    <th><?= $this->Paginator->sort('bigint_col') ?></th>
                    <th><?= $this->Paginator->sort('decimal_col') ?></th>
                    <th><?= $this->Paginator->sort('float_col') ?></th>
                    <th><?= $this->Paginator->sort('double_col') ?></th>
                    <th><?= $this->Paginator->sort('date_col') ?></th>
                    <th><?= $this->Paginator->sort('time_col') ?></th>
                    <th><?= $this->Paginator->sort('datetime_col') ?></th>
                    <th><?= $this->Paginator->sort('char_col') ?></th>
                    <th><?= $this->Paginator->sort('varchar_col') ?></th>
                    <th><?= $this->Paginator->sort('json_col') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mySqlTypeSamples as $mySqlTypeSample): ?>
                <tr>
                    <td><?= h($mySqlTypeSample->id) ?></td>
                    <td><?= $mySqlTypeSample->int_col === null ? '' : $this->Number->format($mySqlTypeSample->int_col) ?></td>
                    <td><?= $mySqlTypeSample->bigint_col === null ? '' : $this->Number->format($mySqlTypeSample->bigint_col) ?></td>
                    <td><?= $mySqlTypeSample->decimal_col === null ? '' : $this->Number->format($mySqlTypeSample->decimal_col) ?></td>
                    <td><?= $mySqlTypeSample->float_col === null ? '' : $this->Number->format($mySqlTypeSample->float_col) ?></td>
                    <td><?= $mySqlTypeSample->double_col === null ? '' : $this->Number->format($mySqlTypeSample->double_col) ?></td>
                    <td><?= h($mySqlTypeSample->date_col) ?></td>
                    <td><?= h($mySqlTypeSample->time_col) ?></td>
                    <td><?= h($mySqlTypeSample->datetime_col) ?></td>
                    <td><?= h($mySqlTypeSample->char_col) ?></td>
                    <td><?= h($mySqlTypeSample->varchar_col) ?></td>
                    <td><?= h($mySqlTypeSample->json_col) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $mySqlTypeSample->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $mySqlTypeSample->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $mySqlTypeSample->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $mySqlTypeSample->id),
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