<?php

// debug($this->Paginator->getTemplates());

?>
<!-- Search Form -->
<div class="card mb-3">
    <div class="card-header search-toggle" id="searchToggle">
        <span>検索条件</span>
        <span id="searchIcon">▼</span>
    </div>

    <div class="card-body" id="searchBody">
        <form method="get">
            <div class="row mb-2">
                <div class="col-md-3">
                    <label class="form-label">INT 範囲</label>
                    <input 
                        type="number" 
                        name="int_col_from" 
                        class="form-control mb-1"
                        value="<?= h($this->getRequest()->getQuery('int_col_from', '')) ?>"
                    >
                    <input 
                        type="number" 
                        name="int_col_to" 
                        class="form-control"
                        value="<?= h($this->getRequest()->getQuery('int_col_to', '')) ?>"
                    >
                </div>
                <div class="col-md-3">
                    <label class="form-label">BIGINT 範囲</label>
                    <input 
                        type="number" 
                        name="bigint_col_from" 
                        class="form-control mb-1"
                        value="<?= h($this->getRequest()->getQuery('bigint_col_from', '')) ?>"
                    >
                    <input 
                        type="number" 
                        name="bigint_col_to" 
                        class="form-control"
                        value="<?= h($this->getRequest()->getQuery('bigint_col_to', '')) ?>"
                    >
                </div>
                <div class="col-md-3">
                    <label class="form-label">DECIMAL 範囲</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="decimal_from" 
                        class="form-control mb-1" 
                        placeholder="From"
                        value="<?= h($this->getRequest()->getQuery('decimal_from', '')) ?>"
                    >
                    <input 
                        type="number" 
                        step="0.01" 
                        name="decimal_to" 
                        class="form-control" 
                        placeholder="To"
                        value="<?= h($this->getRequest()->getQuery('decimal_to', '')) ?>"
                    >   
                </div>
                <div class="col-md-3">
                    <label class="form-label">FLOAT 範囲</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="float_from" 
                        class="form-control mb-1" 
                        placeholder="From"
                        value="<?= h($this->getRequest()->getQuery('float_from', '')) ?>"
                    >
                    <input 
                        type="number" 
                        step="0.01" 
                        name="float_to" 
                        class="form-control" 
                        placeholder="To"
                        value="<?= h($this->getRequest()->getQuery('float_to', '')) ?>"
                    >
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3">
                    <label class="form-label">DATE 範囲</label>
                    <input 
                        type="date" 
                        name="date_from" 
                        class="form-control mb-1"
                        value="<?= h($this->getRequest()->getQuery('date_from', '')) ?>"
                    >
                    <input 
                        type="date" 
                        name="date_to" 
                        class="form-control"
                        value="<?= h($this->getRequest()->getQuery('date_to', '')) ?>"
                    >
                </div>
                <div class="col-md-3">
                    <label class="form-label">TIME 範囲</label>
                    <input 
                        type="time" 
                        name="time_from" 
                        class="form-control mb-1"
                        value="<?= h($this->getRequest()->getQuery('time_from', '')) ?>"
                    >
                    <input 
                        type="time" 
                        name="time_to" 
                        class="form-control"
                        value="<?= h($this->getRequest()->getQuery('time_to', '')) ?>"
                    >
                </div>
                <div class="col-md-3">
                    <label class="form-label">DATETIME 範囲</label>
                    <input 
                        type="datetime-local" 
                        name="datetime_from" 
                        class="form-control mb-1"
                        step="1"
                        value="<?= h($this->getRequest()->getQuery('datetime_from', '')) ?>"
                    >
                    <input 
                        type="datetime-local" 
                        name="datetime_to" 
                        class="form-control"
                        step="1"
                        value="<?= h($this->getRequest()->getQuery('datetime_to', '')) ?>"
                    >
                </div>
                <div class="col-md-3">
                    <label class="form-label">ID 完全一致</label>
                    <input 
                        type="text" 
                        name="id" 
                        class="form-control mb-1"
                        value="<?= h($this->getRequest()->getQuery('id', '')) ?>"
                    >
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    <label class="form-label">キーワード検索</label>
                    <input 
                        type="text" 
                        name="keyword" 
                        class="form-control" 
                        placeholder="全文検索"
                        value="<?= h($this->getRequest()->getQuery('keyword', '')) ?>"
                    >
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-12">
                    <label class="form-label">表示設定</label>
                    <div class="form-control">
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle all_check_show_fields" 
                                type="checkbox" 
                            >全て
                        </label>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="show_fields[]"
                                value="col_id"
                                <?= h(in_array('col_id', $this->getRequest()->getQuery('show_fields', [])) ? 'checked' : '') ?>
                            >ID
                        </label>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="show_fields[]"
                                value="col_int"
                                <?= h(in_array('col_int', $this->getRequest()->getQuery('show_fields', [])) ? 'checked' : '') ?>
                            >INT
                        </label>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="show_fields[]"
                                value="col_bigint"
                                <?= h(in_array('col_bigint', $this->getRequest()->getQuery('show_fields', [])) ? 'checked' : '') ?>
                            >BIGINT
                        </label>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="show_fields[]"
                                value="col_decimal"
                                <?= h(in_array('col_decimal', $this->getRequest()->getQuery('show_fields', [])) ? 'checked' : '') ?>
                            >DECIMAL
                        </label>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="show_fields[]"
                                value="col_float"
                                <?= h(in_array('col_float', $this->getRequest()->getQuery('show_fields', [])) ? 'checked' : '') ?>
                            >FLOAT
                        </label>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="show_fields[]"
                                value="col_double"
                                <?= h(in_array('col_double', $this->getRequest()->getQuery('show_fields', [])) ? 'checked' : '') ?>
                            >DOUBLE
                        </label>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="show_fields[]"
                                value="col_date"
                                <?= h(in_array('col_date', $this->getRequest()->getQuery('show_fields', [])) ? 'checked' : '') ?>
                            >DATE
                        </label>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="show_fields[]"
                                value="col_time"
                                <?= h(in_array('col_time', $this->getRequest()->getQuery('show_fields', [])) ? 'checked' : '') ?>
                            >TIME
                        </label>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="show_fields[]"
                                value="col_datetime"
                                <?= h(in_array('col_datetime', $this->getRequest()->getQuery('show_fields', [])) ? 'checked' : '') ?>
                            >DATETIME
                        </label>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="show_fields[]"
                                value="col_char"
                                <?= h(in_array('col_char', $this->getRequest()->getQuery('show_fields', [])) ? 'checked' : '') ?>
                            >CHAR
                        </label>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="show_fields[]"
                                value="col_varchar"
                                <?= h(in_array('col_varchar', $this->getRequest()->getQuery('show_fields', [])) ? 'checked' : '') ?>
                            >VARCHAR
                        </label>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="show_fields[]"
                                value="col_text"
                                <?= h(in_array('col_text', $this->getRequest()->getQuery('show_fields', [])) ? 'checked' : '') ?>
                            >TEXT
                        </label>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="show_fields[]"
                                value="col_mediumtext"
                                <?= h(in_array('col_mediumtext', $this->getRequest()->getQuery('show_fields', [])) ? 'checked' : '') ?>
                            >MEDIUMTEXT
                        </label>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="show_fields[]"
                                value="col_longtext"
                                <?= h(in_array('col_longtext', $this->getRequest()->getQuery('show_fields', [])) ? 'checked' : '') ?>
                            >LONGTEXT
                        </label>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="show_fields[]"
                                value="col_json"
                                <?= h(in_array('col_json', $this->getRequest()->getQuery('show_fields', [])) ? 'checked' : '') ?>
                            >JSON
                        </label>
                        <script>(function() {

                            function showFieldsToggle() {
                                const checkboxes = document.querySelectorAll('[name="show_fields[]"]');
                                const allFields = Array.from(checkboxes)
                                    .map(cb => 'th.' + cb.value + ',td.' + cb.value).join(',');
                                const selectedFields = Array.from(checkboxes)
                                    .filter(cb => cb.checked)
                                    .map(cb => 'th.' + cb.value + ',td.' + cb.value).join(',');

                                // 全ての列を一旦非表示
                                document.querySelectorAll(allFields).forEach(el => el.style.display = 'none');
                                document.querySelectorAll(selectedFields).forEach(el => el.style.display = '');
                            }

                            // 全てのフィールド表示チェックボックスの制御
                            document.querySelector('.all_check_show_fields').addEventListener('change', function() {
                                const checkboxes = document.querySelectorAll('[name="show_fields[]"]');
                                checkboxes.forEach(cb => cb.checked = this.checked);

                                showFieldsToggle();
                            });
                            
                            document.querySelectorAll('[name="show_fields[]"]').forEach(cb => {
                                cb.addEventListener('change', function() {
                                    const allCheckbox = document.querySelector('.all_check_show_fields');
                                    const checkboxes = document.querySelectorAll('[name="show_fields[]"]');
                                    allCheckbox.checked = Array.from(checkboxes).every(cb => cb.checked);

                                    showFieldsToggle();
                                });
                            });

                            document.addEventListener('DOMContentLoaded', function() {
                                const allCheckbox = document.querySelector('.all_check_show_fields');
                                const checkboxes = document.querySelectorAll('[name="show_fields[]"]');
                                allCheckbox.checked = Array.from(checkboxes).every(cb => cb.checked);

                                showFieldsToggle();
                            });


                        })();</script>
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <button class="btn btn-primary me-2">検索</button>
            </div>
        </form>
    </div>
</div>
<!-- Pagination Top -->
<p><?= $this->Paginator->counter(__('全{{count}}件中 {{start}}-{{end}}を表示')) ?></p>
<div class="d-flex flex-wrap align-items-center gap-3 mb-2">
    <!-- 左：ページネーション -->
    <div class="order-1">
        <nav>
            <ul class="pagination mb-0">
                <?= $this->Paginator->first(__('First')) ?>
                <?= $this->Paginator->prev( __('Prev')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('Next')) ?>
                <?= $this->Paginator->last(__('Last')) ?>
            </ul>
        </nav>
    </div>
    <!-- 中央：ボタン -->
    <div class="order-2 mx-auto d-flex gap-2">
        <button class="btn btn-sm btn-success">一括更新</button>
        <button class="btn btn-sm btn-danger">一括削除</button>
        <button class="btn btn-sm btn-secondary">エクスポート</button>
    </div>
    <!-- 右：件数表示 -->
    <div class="order-3 ms-auto">
        <select class="form-select form-select-sm w-auto">
            <option>10件表示</option>
            <option>20件表示</option>
            <option>50件表示</option>
            <option>100件表示</option>
        </select>
    </div>
</div>

<!-- Result Table -->
<style>
    .table th.col_choice, .table td.col_choice { width: 40px; min-width: 40px; max-width: 40px; }
    .table th.col_id, .table td.col_id { width: 140px; min-width: 140px; max-width: 140px; }
    .table th.col_int, .table td.col_int { width: 140px; min-width: 140px; max-width: 140px; }
    .table th.col_bigint, .table td.col_bigint { width: 140px; min-width: 140px; max-width: 140px; }
    .table th.col_decimal, .table td.col_decimal { width: 140px; min-width: 140px; max-width: 140px; }
    .table th.col_float, .table td.col_float { width: 140px; min-width: 140px; max-width: 140px; }
    .table th.col_double, .table td.col_double { width: 140px; min-width: 140px; max-width: 140px; }
    .table th.col_date, .table td.col_date { width: 140px; min-width: 140px; max-width: 140px; }
    .table th.col_time, .table td.col_time { width: 140px; min-width: 140px; max-width: 140px; }
    .table th.col_datetime, .table td.col_datetime { width: 140px; min-width: 140px; max-width: 140px; }
    .table th.col_char, .table td.col_char { width: 140px; min-width: 140px; max-width: 140px; }
    .table th.col_varchar, .table td.col_varchar { width: 140px; min-width: 140px; max-width: 140px; }
    .table th.col_text, .table td.col_text { width: 140px; min-width: 140px; max-width: 140px; }
    .table th.col_mediumtext, .table td.col_mediumtext { width: 140px; min-width: 140px; max-width: 140px; }
    .table th.col_longtext, .table td.col_longtext { width: 140px; min-width: 140px; max-width: 140px; }
    .table th.col_json, .table td.col_json { width: 140px; min-width: 140px; max-width: 140px; }
    .table th.col_action, .table td.col_action { width: 180px; min-width: 180px; max-width: 180px; }
</style>
<div class="table-scroll mb-2">
    <table class="table table-bordered table-sm table-hover">
        <thead>
            <tr>
                <th class="col_choice">
                    <input type="checkbox" id="checkAll">
                </th>
                <th class="col_id"><?= $this->Paginator->sort('id', 'ID') ?></th>
                <th class="col_int"><?= $this->Paginator->sort('int_col', 'INT') ?></th>
                <th class="col_bigint"><?= $this->Paginator->sort('bigint_col', 'BIGINT') ?></th>
                <th class="col_decimal"><?= $this->Paginator->sort('decimal_col', 'DECIMAL') ?></th>
                <th class="col_float"><?= $this->Paginator->sort('float_col', 'FLOAT') ?></th>
                <th class="col_double"><?= $this->Paginator->sort('double_col', 'DOUBLE') ?></th>
                <th class="col_date"><?= $this->Paginator->sort('date_col', 'DATE') ?></th>
                <th class="col_time"><?= $this->Paginator->sort('time_col', 'TIME') ?></th>
                <th class="col_datetime"><?= $this->Paginator->sort('datetime_col', 'DATETIME') ?></th>
                <th class="col_char"><?= $this->Paginator->sort('char_col', 'CHAR') ?></th>
                <th class="col_varchar"><?= $this->Paginator->sort('varchar_col', 'VARCHAR') ?></th>
                <th class="col_text"><?= $this->Paginator->sort('text_col', 'TEXT') ?></th>
                <th class="col_mediumtext"><?= $this->Paginator->sort('mediumtext_col', 'MEDIUMTEXT') ?></th>
                <th class="col_longtext"><?= $this->Paginator->sort('longtext_col', 'LONGTEXT') ?></th>
                <th class="col_json"><?= $this->Paginator->sort('json_col', 'JSON') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row) : ?>
            <tr>
                <td class="col_choice text-center">
                    <input type="checkbox" class="row-check">
                </td>
                <td class="col_id"><input class="result-input text-center" value="<?= h($row['id']) ?>"></td>
                <td class="col_int"><input class="result-input text-right" value="<?= h($row['int_col']) ?>"></td>
                <td class="col_bigint"><input class="result-input text-right" value="<?= h($row['bigint_col']) ?>"></td>
                <td class="col_decimal"><input class="result-input text-right" value="<?= h($row['decimal_col']) ?>"></td>
                <td class="col_float"><input class="result-input text-right" value="<?= h($row['float_col']) ?>"></td>
                <td class="col_double"><input class="result-input text-right" value="<?= h($row['double_col']) ?>"></td>
                <td class="col_date"><input class="result-input text-center" value="<?= h($row['date_col']) ?>"></td>
                <td class="col_time"><input class="result-input text-center" value="<?= h($row['time_col']) ?>"></td>
                <td class="col_datetime"><input class="result-input text-center" value="<?= h($row['datetime_col']) ?>"></td>
                <td class="col_char"><input class="result-input" value="<?= h($row['char_col']) ?>"></td>
                <td class="col_varchar"><input class="result-input" value="<?= h($row['varchar_col']) ?>"></td>
                <td class="col_text"><input class="result-input" value="<?= h($row['text_col']) ?>"></td>
                <td class="col_mediumtext"><input class="result-input" value="<?= h($row['mediumtext_col']) ?>"></td>
                <td class="col_longtext"><input class="result-input" value="<?= h($row['longtext_col']) ?>"></td>
                <td class="col_json"><input class="result-input" value="<?= h($row['json_col']) ?>"></td>
                <td class="col_action text-center">
                    <button class="btn btn-sm btn-info me-2">更新</button>
                    <button class="btn btn-sm btn-info me-2">詳細</button>
                    <button class="btn btn-sm btn-info me-2">複製</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="d-flex flex-wrap align-items-center gap-3 mb-2">
    <!-- 左：ページネーション -->
    <div class="order-1">
        <nav>
            <ul class="pagination mb-0">
                <?= $this->Paginator->first(__('First')) ?>
                <?= $this->Paginator->prev( __('Prev')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('Next')) ?>
                <?= $this->Paginator->last(__('Last')) ?>
            </ul>
        </nav>
    </div>
    <!-- 中央：ボタン -->
    <div class="order-2 mx-auto d-flex gap-2">
        <button class="btn btn-sm btn-success">一括更新</button>
        <button class="btn btn-sm btn-danger">一括削除</button>
        <button class="btn btn-sm btn-secondary">エクスポート</button>
    </div>
    <!-- 右：件数表示 -->
    <div class="order-3 ms-auto">
        <select class="form-select form-select-sm w-auto">
            <option>10件表示</option>
            <option>20件表示</option>
            <option>50件表示</option>
            <option>100件表示</option>
        </select>
    </div>
</div>
<p><?= $this->Paginator->counter(__('全{{count}}件中 {{start}}-{{end}}を表示')) ?></p>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const checkAll = document.getElementById("checkAll");

    // 全選択 / 全解除
    checkAll.addEventListener("change", function () {

        document.querySelectorAll(".row-check").forEach(chk => chk.checked = checkAll.checked);
    });

    // 個別チェック → ヘッダーcheckbox同期
    document.addEventListener("change", function (e) {
        if (e.target.classList.contains("row-check")) {
            const rows = document.querySelectorAll(".row-check");
            const allChecked = [...rows].every(chk => chk.checked);
            const someChecked = [...rows].some(chk => chk.checked);

            checkAll.checked = allChecked;
            checkAll.indeterminate = !allChecked && someChecked; // 中間状態
        }
    });
});

document.getElementById("searchToggle").addEventListener("click", function() {
    const body = document.getElementById("searchBody");
    const icon = document.getElementById("searchIcon");

    if (body.style.display === "none") {
        body.style.display = "block";
        icon.textContent = "▼";
    } else {
        body.style.display = "none";
        icon.textContent = "▲";
    }
});

$(document).ready(function() {

    $('.select2').select2({
        width: '100%',
        placeholder: "選択してください",
        allowClear: true
    });

});
</script>