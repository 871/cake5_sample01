<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MySqlTypeSample $mySqlTypeSample
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit My Sql Type Sample'), ['action' => 'edit', $mySqlTypeSample->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete My Sql Type Sample'), ['action' => 'delete', $mySqlTypeSample->id], ['confirm' => __('Are you sure you want to delete # {0}?', $mySqlTypeSample->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List My Sql Type Samples'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New My Sql Type Sample'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="mySqlTypeSamples view content">
            <h3><?= h($mySqlTypeSample->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($mySqlTypeSample->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Char Col') ?></th>
                    <td><?= h($mySqlTypeSample->char_col) ?></td>
                </tr>
                <tr>
                    <th><?= __('Varchar Col') ?></th>
                    <td><?= h($mySqlTypeSample->varchar_col) ?></td>
                </tr>
                <tr>
                    <th><?= __('Json Col') ?></th>
                    <td><?= h($mySqlTypeSample->json_col) ?></td>
                </tr>
                <tr>
                    <th><?= __('Int Col') ?></th>
                    <td><?= $mySqlTypeSample->int_col === null ? '' : $this->Number->format($mySqlTypeSample->int_col) ?></td>
                </tr>
                <tr>
                    <th><?= __('Bigint Col') ?></th>
                    <td><?= $mySqlTypeSample->bigint_col === null ? '' : $this->Number->format($mySqlTypeSample->bigint_col) ?></td>
                </tr>
                <tr>
                    <th><?= __('Decimal Col') ?></th>
                    <td><?= $mySqlTypeSample->decimal_col === null ? '' : $this->Number->format($mySqlTypeSample->decimal_col) ?></td>
                </tr>
                <tr>
                    <th><?= __('Float Col') ?></th>
                    <td><?= $mySqlTypeSample->float_col === null ? '' : $this->Number->format($mySqlTypeSample->float_col) ?></td>
                </tr>
                <tr>
                    <th><?= __('Double Col') ?></th>
                    <td><?= $mySqlTypeSample->double_col === null ? '' : $this->Number->format($mySqlTypeSample->double_col) ?></td>
                </tr>
                <tr>
                    <th><?= __('Date Col') ?></th>
                    <td><?= h($mySqlTypeSample->date_col) ?></td>
                </tr>
                <tr>
                    <th><?= __('Time Col') ?></th>
                    <td><?= h($mySqlTypeSample->time_col) ?></td>
                </tr>
                <tr>
                    <th><?= __('Datetime Col') ?></th>
                    <td><?= h($mySqlTypeSample->datetime_col) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Text Col') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($mySqlTypeSample->text_col)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Mediumtext Col') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($mySqlTypeSample->mediumtext_col)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Longtext Col') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($mySqlTypeSample->longtext_col)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Search Text') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($mySqlTypeSample->search_text)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>