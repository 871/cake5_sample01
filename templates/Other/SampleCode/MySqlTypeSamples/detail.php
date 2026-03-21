<?php
/* @var \App\Domain\Sample\MySqlTypeSamples\Entity\MySqlTypeSample $entity */

?>
<div class="card shadow-sm">
    <div class="card-header bg-info text-white">
        詳細表示
    </div>

    <div class="card-body">

        <table class="table table-bordered">
            <tr>
                <th style="width:30%">ID</th>
                <td><?= h($entity->id()) ?></td>
            </tr>
        </table>

        <!-- 数値系 -->
        <h6 class="border-bottom pb-2 mb-3">数値系</h6>

        <table class="table table-bordered">
            <tr>
                <th style="width:30%">INT</th>
                <td><?= h($entity->intCol()) ?></td>
            </tr>
            <tr>
                <th>BIGINT</th>
                <td><?= h($entity->bigintCol()) ?></td>
            </tr>
            <tr>
                <th>DECIMAL</th>
                <td><?= h($entity->decimalCol()) ?></td>
            </tr>
            <tr>
                <th>FLOAT</th>
                <td><?= h($entity->floatCol()) ?></td>
            </tr>
        </table>

        <!-- 日付系 -->
        <h6 class="border-bottom pb-2 mb-3">日付系</h6>

        <table class="table table-bordered">
            <tr>
                <th style="width:30%">DATE</th>
                <td><?= h($entity->dateCol()?->format('Y/m/d') ?? '----/--/--') ?></td>
            </tr>
            <tr>
                <th>TIME</th>
                <td><?= h($entity->timeCol()?->format('H:i:s') ?? '--:--:--') ?></td>
            </tr>
            <tr>
                <th>DATETIME</th>
                <td><?= h($entity->datetimeCol()?->format('Y/m/d H:i:s') ?? '----/--/-- --:--:--') ?></td>
            </tr>
        </table>

        <!-- 文字列系 -->
        <h6 class="border-bottom pb-2 mb-3">文字列系</h6>

        <table class="table table-bordered">
            <tr>
                <th style="width:30%">CHAR</th>
                <td><?= h($entity->charCol()) ?></td>
            </tr>
            <tr>
                <th>VARCHAR</th>
                <td><?= h($entity->varcharCol()) ?></td>
            </tr>
            <tr>
                <th>TEXT</th>
                <td style="white-space:pre-wrap">
                    <?= nl2br(h($entity->textCol())) ?>
                </td>
            </tr>
            <tr>
                <th>JSON</th>
                <td>
                    <?= h($entity->jsonCol()) ?>
                </td>
            </tr>
        </table>

        <div class="text-center mt-4">
            <a href="<?= $this->Url->build([
                'controller' => 'Search',
                'action' => 'index',
                '?' => $this->getRequest()->getQuery(),
            ]) ?>" class="btn btn-secondary px-5">戻る</a>
            <a href="<?= $this->Url->build([
                'controller' => 'Edit',
                'action' => 'index',
                'my_sql_type_sample_id' => $entity->id(),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>" class="btn btn btn-primary px-5">更新</a>
            <a href="<?= $this->Url->build([
                'controller' => 'Create',
                'action' => 'copy',
                'my_sql_type_sample_id' => $entity->id(),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>" class="btn btn btn-primary px-5">複製</a>
            <?= $this->Form->postLink('削除', [
                'controller' => 'Delete',
                'action' => 'index',
                'my_sql_type_sample_id' => $entity->id()->toString(),
                '?' => $this->getRequest()->getQuery(),
            ], [
                'class' => 'btn btn btn-danger px-5',
                'confirm' => '削除しますか？'
            ]) ?>
        </div>
    </div>
</div>