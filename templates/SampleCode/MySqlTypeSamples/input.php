<?php

use App\Domain\Sample\MySqlTypeSamples\ValueObject as Vo;

/* @var App\Service\Controller\Shared\Process\Process\InputProcess $input */

?>
<!-- Form Card -->
<div class="card shadow-sm">
<?php foreach ($input->getInput('_errorMessages') as $message) { ?>
    <div class="alert alert-error-custom" onclick="this.style.display='none'">
        <?=  h($message) ?>
    </div>
<?php } ?>
    <div class="card-header bg-primary text-white">
        新規登録
    </div>

    <div class="card-body">
        <form method="post">
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">
            <input type="hidden" name="_process_key" value="<?= h($input->getInput('_process_key')) ?>">

            <h6 class="border-bottom pb-2 mb-3">ID</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <input 
                        type="text" 
                        name="int_col" 
                        class="form-control"
                        value="<?= h($input->getInput('id', '（新規作成）')) ?>"
                        readonly
                    >
                </div>
            </div>


            <h6 class="border-bottom pb-2 mb-3">数値系</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">INT</label>
                    <input 
                        type="number" 
                        name="int_col" 
                        class="form-control <?= h($input->getInput('_errorFields.int_col')) ?>"
                        value="<?= h($input->getInput('int_col')) ?>"
                        step="<?= h(Vo\IntCol::STEP) ?>"
                        min="<?= h(Vo\IntCol::MIN) ?>"
                        max="<?= h(Vo\IntCol::MAX) ?>"
                    >
                </div>
                <div class="col-md-12">
                    <label class="form-label">BIGINT</label>
                    <input 
                        type="number" 
                        name="bigint_col" 
                        class="form-control <?= h($input->getInput('_errorFields.bigint_col')) ?>"
                        value="<?= h($input->getInput('bigint_col')) ?>"
                        step="<?= h(Vo\BigintCol::STEP) ?>"
                        min="<?= h(Vo\BigintCol::MIN) ?>"
                        max="<?= h(Vo\BigintCol::MAX) ?>"
                    >
                </div>
                <div class="col-md-12">
                    <label class="form-label">DECIMAL</label>
                    <input 
                        type="number" 
                        name="decimal_col" 
                        class="form-control <?= h($input->getInput('_errorFields.decimal_col')) ?>"
                        value="<?= h($input->getInput('decimal_col')) ?>"
                        step="<?= h(Vo\DecimalCol::STEP) ?>"
                        min="<?= h(Vo\DecimalCol::MIN) ?>"
                        max="<?= h(Vo\DecimalCol::MAX) ?>"
                    >
                </div>
                <div class="col-md-12">
                    <label class="form-label">FLOAT</label>
                    <input 
                        type="number" 
                        name="float_col" 
                        class="form-control <?= h($input->getInput('_errorFields.float_col')) ?>"
                        value="<?= h($input->getInput('float_col')) ?>"
                        step="<?= h(Vo\FloatCol::STEP) ?>"
                        min="<?= h(Vo\FloatCol::MIN) ?>"
                        max="<?= h(Vo\FloatCol::MAX) ?>"
                    >
                </div>
            </div>

            <!-- 日付系 -->
            <h6 class="border-bottom pb-2 mb-3">日付系</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">DATE</label>
                    <input 
                        type="date" 
                        name="date_col" 
                        class="form-control <?= h($input->getInput('_errorFields.date_col')) ?>"
                        value="<?= h($input->getInput('date_col')) ?>"
                        min="<?= h(Vo\DateCol::MIN) ?>"
                        max="<?= h(Vo\DateCol::MAX) ?>"
                    >
                </div>
                <div class="col-md-12">
                    <label class="form-label">TIME</label>
                    <input 
                        type="time" 
                        name="time_col" 
                        class="form-control <?= h($input->getInput('_errorFields.time_col')) ?>"
                        value="<?= h($input->getInput('time_col')) ?>"
                        step="1"
                        min="<?= h(Vo\TimeCol::MIN) ?>"
                        max="<?= h(Vo\TimeCol::MAX) ?>"
                    >
                </div>
                <div class="col-md-12">
                    <label class="form-label">DATETIME</label>
                    <input 
                        type="datetime-local" 
                        name="datetime_col" 
                        class="form-control <?= h($input->getInput('_errorFields.datetime_col')) ?>"
                        value="<?= h($input->getInput('datetime_col')) ?>"
                        step="1"
                        min="<?= h(Vo\DatetimeCol::MIN) ?>"
                        max="<?= h(Vo\DatetimeCol::MAX) ?>"
                    >
                </div>
            </div>

            <!-- 文字列系 -->
            <h6 class="border-bottom pb-2 mb-3">文字列系</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">CHAR</label>
                    <input 
                        type="text" 
                        name="char_col" 
                        class="form-control <?= h($input->getInput('_errorFields.char_col')) ?>"
                        value="<?= h($input->getInput('char_col')) ?>"
                        maxlength="10"
                    >
                </div>
                <div class="col-md-12">
                    <label class="form-label">VARCHAR</label>
                    <input 
                        type="text" 
                        name="varchar_col" 
                        class="form-control <?= h($input->getInput('_errorFields.varchar_col')) ?>"
                        value="<?= h($input->getInput('varchar_col')) ?>"
                        maxlength="255"
                    >
                </div>
                <div class="col-md-12">
                    <label class="form-label">TEXT</label>
                    <textarea 
                        id="textEditor" 
                        name="text_col"
                        class="form-control <?= h($input->getInput('_errorFields.text_col')) ?>"
                    ><?= h($input->getInput('text_col')) ?></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">JSONデータ</label>
                    <textarea 
                        name="json_col"
                        class="form-control <?= h($input->getInput('_errorFields.json_col')) ?>"
                    ><?= h($input->getInput('json_col')) ?></textarea>
                </div>
            </div>
            <!-- ボタン -->
            <div class="text-center mt-4">
                <a href="<?= $this->Url->build([
                    'controller' => 'Search',
                    'action' => 'index',
                    '?' => $this->getRequest()->getQuery(),
                ]) ?>" class="btn btn-secondary px-5">戻る</a>
                <button type="submit" class="btn btn-primary px-5 me-3">確認へ</button>
            </div>
        </form>
    </div>
</div>