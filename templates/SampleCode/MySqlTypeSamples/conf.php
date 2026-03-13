<?php

use App\Domain\Sample\MySqlTypeSamples\ValueObject as Vo;

/* @var App\Service\Controller\Shared\Process\Process\InputProcess $input */

?>
<div class="card shadow-sm">
    <div class="card-header bg-success text-white">
        入力内容確認
    </div>

    <div class="card-body">
        <form method="post">
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">
            <input type="hidden" name="_process_key" value="<?= h($input->getInput('_process_key')) ?>">
            <table class="table table-bordered">
                <tr>
                    <th style="width:30%">ID</th>
                    <td><?= h($input->getInput('id', '（新規作成）')) ?></tr>
                </tr>
            </table>
            <!-- 数値系 -->
            <h6 class="border-bottom pb-2 mb-3">数値系</h6>

            <table class="table table-bordered">
                <tr>
                    <th style="width:30%">INT</th>
                    <td><?= h($input->getInput('int_col')) ?></td>
                </tr>
                <tr>
                    <th>BIGINT</th>
                    <td><?= h($input->getInput('bigint_col')) ?></td>
                </tr>
                <tr>
                    <th>DECIMAL</th>
                    <td><?= h($input->getInput('decimal_col')) ?></td>
                </tr>
                <tr>
                    <th>FLOAT</th>
                    <td><?= h($input->getInput('float_col')) ?></td>
                </tr>
            </table>

            <!-- 日付系 -->
            <h6 class="border-bottom pb-2 mb-3">日付系</h6>

            <table class="table table-bordered">
                <tr>
                    <th style="width:30%">DATE</th>
                    <td><?= h($input->getInput('date_col')) ?></td>
                </tr>
                <tr>
                    <th>TIME</th>
                    <td><?= h($input->getInput('time_col')) ?></td>
                </tr>
                <tr>
                    <th>DATETIME</th>
                    <td><?= h($input->getInput('datetime_col')) ?></td>
                </tr>
            </table>

            <!-- 文字列系 -->
            <h6 class="border-bottom pb-2 mb-3">文字列系</h6>

            <table class="table table-bordered">
                <tr>
                    <th style="width:30%">CHAR</th>
                    <td><?= h($input->getInput('char_col')) ?></td>
                </tr>
                <tr>
                    <th>VARCHAR</th>
                    <td><?= h($input->getInput('varchar_col')) ?></td>
                </tr>
                <tr>
                    <th>TEXT</th>
                    <td style="white-space:pre-wrap"><?= h($input->getInput('text_col')) ?></td>
                </tr>
                <tr>
                    <th>JSON</th>
                    <td>
                        <pre class="bg-light p-2"><?= h($input->getInput('json_col')) ?></pre>
                    </td>
                </tr>
            </table>

            <div class="text-center mt-4">
                <a href="<?= $this->Url->build([
                    'action' => 'input',
                    'process_id'=> $this->getRequest()->getParam('process_id'),
                    '?' => $this->getRequest()->getQuery(),
                ]) ?>" class="btn btn-secondary px-5">修正する</a>
                <button type="submit" name="_process_action" value="complete" class="btn btn-success px-5 me-3">
                    登録する
                </button>
            </div>
        </form>
    </div>
</div>