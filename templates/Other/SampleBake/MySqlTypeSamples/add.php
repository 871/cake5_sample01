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
            <?= $this->Html->link(__('List My Sql Type Samples'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="mySqlTypeSamples form content">
            <?= $this->Form->create($mySqlTypeSample) ?>
            <fieldset>
                <legend><?= __('Add My Sql Type Sample') ?></legend>
                <?php
                    echo $this->Form->control('int_col');
                    echo $this->Form->control('bigint_col');
                    echo $this->Form->control('decimal_col');
                    echo $this->Form->control('float_col');
                    echo $this->Form->control('double_col');
                    echo $this->Form->control('date_col', ['empty' => true]);
                    echo $this->Form->control('time_col', ['empty' => true]);
                    echo $this->Form->control('datetime_col', ['empty' => true]);
                    echo $this->Form->control('char_col');
                    echo $this->Form->control('varchar_col');
                    echo $this->Form->control('text_col');
                    echo $this->Form->control('mediumtext_col');
                    echo $this->Form->control('longtext_col');
                    echo $this->Form->control('json_col');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
