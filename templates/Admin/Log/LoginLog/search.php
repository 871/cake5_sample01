<?php
use App\Model\Entity\Log\LoginLog;

/* @var \Cake\View\View $this */
/* @var \Cake\Datasource\ResultSetInterface<\App\Model\Entity\Log\LoginLog> $rows */
?>
<!-- 検索フォーム -->
<div class="card mb-3">
    <div class="card-header bg-secondary text-white">
        ログイン試行ログ検索
    </div>
    <div class="card-body">
        <form method="get" action="<?= $this->Url->build(['action' => 'index']) ?>">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">ログインID</label>
                    <input
                        type="text"
                        name="login_id"
                        class="form-control"
                        value="<?= h($this->getRequest()->getQuery('login_id')) ?>"
                    >
                </div>
                <div class="col-md-2">
                    <label class="form-label">アクター種別</label>
                    <select name="login_actor_type" class="form-select">
                        <option value="">（全て）</option>
                        <option value="ADMIN"
                            <?= $this->getRequest()->getQuery('login_actor_type') === 'ADMIN' ? 'selected' : '' ?>>
                            ADMIN
                        </option>
                        <option value="USER"
                            <?= $this->getRequest()->getQuery('login_actor_type') === 'USER' ? 'selected' : '' ?>>
                            USER
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">ログイン結果</label>
                    <select name="login_result" class="form-select">
                        <option value="">（全て）</option>
                        <option value="SUCCESS"
                            <?= $this->getRequest()->getQuery('login_result') === 'SUCCESS' ? 'selected' : '' ?>>
                            SUCCESS
                        </option>
                        <option value="FAILURE"
                            <?= $this->getRequest()->getQuery('login_result') === 'FAILURE' ? 'selected' : '' ?>>
                            FAILURE
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">IPアドレス</label>
                    <input
                        type="text"
                        name="ip_address"
                        class="form-control"
                        value="<?= h($this->getRequest()->getQuery('ip_address')) ?>"
                        placeholder="部分一致"
                    >
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">検索</button>
                </div>
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
                    <th><?= $this->Paginator->sort('login_id', 'ログインID') ?></th>
                    <th><?= $this->Paginator->sort('login_actor_type', 'アクター種別') ?></th>
                    <th><?= $this->Paginator->sort('login_result', 'ログイン結果') ?></th>
                    <th><?= $this->Paginator->sort('ip_address', 'IPアドレス') ?></th>
                    <th><?= $this->Paginator->sort('logged_in_at', 'ログイン日時') ?></th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($rows) === 0) { ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">データがありません</td>
                </tr>
            <?php } else { ?>
                <?php foreach ($rows as $row): ?>
                <?php /** @var \App\Model\Entity\Log\LoginLog $row */ ?>
                <tr>
                    <td><?= h($row->login_id) ?></td>
                    <td><?= h($row->login_actor_type) ?></td>
                    <td>
                        <?php if ($row->login_result === 'SUCCESS') { ?>
                            <span class="badge bg-success">SUCCESS</span>
                        <?php } elseif ($row->login_result === 'FAILURE') { ?>
                            <span class="badge bg-danger">FAILURE</span>
                        <?php } else { ?>
                            <?= h($row->login_result) ?>
                        <?php } ?>
                    </td>
                    <td><?= h($row->ip_address) ?></td>
                    <td><?= h($row->logged_in_at?->format('Y/m/d H:i:s') ?? '') ?></td>
                    <td class="text-nowrap">
                        <a href="<?= $this->Url->build([
                            'prefix' => 'Admin/Log/LoginLog',
                            'controller' => 'Detail',
                            'action' => 'index',
                            'account_id' => $this->getRequest()->getParam('account_id'),
                            'login_log_id' => $row->id,
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>" class="btn btn-info btn-sm">詳細</a>
                    </td>
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
