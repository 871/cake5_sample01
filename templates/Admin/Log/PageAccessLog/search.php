<?php
use App\Model\Entity\Log\PageAccessLog;

/* @var \Cake\View\View $this */
/* @var \Cake\Datasource\ResultSetInterface<\App\Model\Entity\Log\PageAccessLog> $rows */
/* @var array<string, string> $accountTypeOptions */

$pageOptions = [
    'url' => array_merge(
        ['account_id' => $this->getRequest()->getParam('account_id')],
        (array)$this->getRequest()->getQuery(),
    ),
];
?>
<!-- 検索フォーム -->
<div class="card mb-3">
    <div class="card-header bg-secondary text-white">
        ページアクセスログ検索
    </div>
    <div class="card-body">
        <form method="get">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">アクセス日時（自）</label>
                    <input
                        type="datetime-local"
                        name="accessed_from"
                        class="form-control"
                        step="1"
                        min="1970-01-01T00:00:00"
                        max="2999-12-31T23:59:59"
                        value="<?= h($this->getRequest()->getQuery('accessed_from')) ?>"
                    >
                </div>
                <div class="col-md-6">
                    <label class="form-label">アクセス日時（至）</label>
                    <input
                        type="datetime-local"
                        name="accessed_to"
                        class="form-control"
                        step="1"
                        min="1970-01-01T00:00:00"
                        max="2999-12-31T23:59:59"
                        value="<?= h($this->getRequest()->getQuery('accessed_to')) ?>"
                    >
                </div>
                <div class="col-md-3">
                    <label class="form-label">アカウント種別</label>
                    <select name="account_type" class="form-select">
                        <option value="">（すべて）</option>
                        <?php foreach ($accountTypeOptions as $value => $label): ?>
                            <option
                                value="<?= h($value) ?>"
                                <?= $this->getRequest()->getQuery('account_type') === $value ? 'selected' : '' ?>
                            ><?= h($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">アカウントID</label>
                    <input
                        type="text"
                        name="account_id"
                        class="form-control"
                        value="<?= h($this->getRequest()->getQuery('account_id')) ?>"
                    >
                </div>
                <div class="col-md-6">
                    <label class="form-label">キーワード</label>
                    <input
                        type="text"
                        name="keyword"
                        class="form-control"
                        placeholder="パス・ルート名・IPアドレス・ユーザーエージェント・アカウント名"
                        value="<?= h($this->getRequest()->getQuery('keyword')) ?>"
                    >
                </div>
            </div>
            <div class="text-center mt-3">
                <button class="btn btn-primary me-2">検索</button>
            </div>
        </form>
    </div>
</div>

<!-- 検索結果 -->
<div class="card">
    <div class="card-header">
        <span>検索結果</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-bordered mb-0">
            <thead class="table-dark">
                <tr>
                    <th><?= $this->Paginator->sort('accessed', 'アクセス日時', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('account_type', 'アカウント種別', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('account_id', 'アカウントID', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('method', 'メソッド', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('path', 'パス', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('ip_address', 'IPアドレス', $pageOptions) ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($rows) === 0) { ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">データがありません</td>
                </tr>
            <?php } else { ?>
                <?php foreach ($rows as $row): ?>
                <?php /** @var \App\Model\Entity\Log\PageAccessLog $row */ ?>
                <tr>
                    <td><?= h($row->accessed?->format('Y/m/d H:i:s') ?? '') ?></td>
                    <td><?= h($row->account_type) ?></td>
                    <td><?= h($row->account_id) ?></td>
                    <td><?= h($row->method) ?></td>
                    <td><?= h($row->path) ?></td>
                    <td><?= h($row->ip_address) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <?= $this->Paginator->counter('全 {{count}} 件中 {{start}}-{{end}} 件') ?>
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?= $this->Paginator->prev('«') ?>
                    <?= $this->Paginator->numbers() ?>
                    <?= $this->Paginator->next('»') ?>
                </ul>
            </nav>
        </div>
    </div>
</div>
