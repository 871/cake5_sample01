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
        <form method="get">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">ログイン日時（自）</label>
                    <input
                        type="datetime-local"
                        name="logged_in_at_from"
                        class="form-control"
                        step="1"
                        min="1970-01-01T00:00:00"
                        max="2999-12-31T23:59:59"
                        value="<?= h($this->getRequest()->getQuery('logged_in_at_from')) ?>"
                    >
                </div>
                <div class="col-md-6">
                    <label class="form-label">ログイン日時（至）</label>
                    <input
                        type="datetime-local"
                        name="logged_in_at_to"
                        class="form-control"
                        step="1"
                        min="1970-01-01T00:00:00"
                        max="2999-12-31T23:59:59"
                        value="<?= h($this->getRequest()->getQuery('logged_in_at_to')) ?>"
                    >
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">キーワード</label>
                    <input
                        type="text"
                        name="keyword"
                        class="form-control"
                        value="<?= h($this->getRequest()->getQuery('keyword')) ?>"
                    >
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">アカウントID</label>
                    <input
                        type="number"
                        name="account_id"
                        class="form-control"
                        min="900000"
                        max="199999999"
                        value="<?= h($this->getRequest()->getQuery('account_id')) ?>"
                    >
                </div>
                <div class="col-md-6">
                    <label class="form-label">代理ログインアカウントID</label>
                    <input
                        type="number"
                        name="impersonator_account_id"
                        class="form-control"
                        min="900000"
                        max="199999999"
                        value="<?= h($this->getRequest()->getQuery('impersonator_account_id')) ?>"
                    >
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">アカウント種別</label>
                    <div class="form-control">
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle all_login_actor_type" 
                                type="checkbox" 
                            >全て
                        </label>
                    <?php foreach ($loginActorTypeOptions as $value => $label) : ?>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="login_actor_type[]"
                                value="<?= h($value) ?>"
                                <?= h(in_array($value, $this->getRequest()->getQuery('login_actor_type', [])) ? 'checked' : '') ?>
                            ><?= h($label) ?>
                        </label>
                    <?php endforeach; ?>
                        <script>(function() {
                            // 全てのフィールド表示チェックボックスの制御
                            document.querySelector('.all_login_actor_type').addEventListener('change', function() {
                                const checkboxes = document.querySelectorAll('[name="login_actor_type[]"]');
                                checkboxes.forEach(cb => cb.checked = this.checked);
                            });
                            
                            document.querySelectorAll('[name="login_actor_type[]"]').forEach(cb => {
                                cb.addEventListener('change', function() {
                                    const allCheckbox = document.querySelector('.all_login_actor_type');
                                    const checkboxes = document.querySelectorAll('[name="login_actor_type[]"]');
                                    allCheckbox.checked = Array.from(checkboxes).every(cb => cb.checked);
                                });
                            });

                            document.addEventListener('DOMContentLoaded', function() {
                                const allCheckbox = document.querySelector('.all_login_actor_type');
                                const checkboxes = document.querySelectorAll('[name="login_actor_type[]"]');
                                allCheckbox.checked = Array.from(checkboxes).every(cb => cb.checked);
                            });
                        })();</script>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ログイン結果</label>
                    <div class="form-control">
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle all_login_result" 
                                type="checkbox" 
                            >全て
                        </label>
                    <?php foreach ($loginResultOptions as $value => $label) : ?>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="login_result[]"
                                value="<?= h($value) ?>"
                                <?= h(in_array($value, $this->getRequest()->getQuery('login_result', [])) ? 'checked' : '') ?>
                            ><?= h($label) ?>
                        </label>
                    <?php endforeach; ?>
                        <script>(function() {
                            // 全てのフィールド表示チェックボックスの制御
                            document.querySelector('.all_login_result').addEventListener('change', function() {
                                const checkboxes = document.querySelectorAll('[name="login_result[]"]');
                                checkboxes.forEach(cb => cb.checked = this.checked);
                            });
                            
                            document.querySelectorAll('[name="login_result[]"]').forEach(cb => {
                                cb.addEventListener('change', function() {
                                    const allCheckbox = document.querySelector('.all_login_result');
                                    const checkboxes = document.querySelectorAll('[name="login_result[]"]');
                                    allCheckbox.checked = Array.from(checkboxes).every(cb => cb.checked);
                                });
                            });

                            document.addEventListener('DOMContentLoaded', function() {
                                const allCheckbox = document.querySelector('.all_login_result');
                                const checkboxes = document.querySelectorAll('[name="login_result[]"]');
                                allCheckbox.checked = Array.from(checkboxes).every(cb => cb.checked);
                            });
                        })();</script>
                    </div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">失敗理由コード</label>
                    <div class="form-control">
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle all_failure_reason_code" 
                                type="checkbox" 
                            >全て
                        </label>
                    <?php foreach ($failureReasonCodeOptions as $value => $label) : ?>
                        <label class="form-check-label">
                            <input 
                                class="form-check-input column-toggle" 
                                type="checkbox" 
                                name="failure_reason_code[]"
                                value="<?= h($value) ?>"
                                <?= h(in_array($value, $this->getRequest()->getQuery('failure_reason_code', [])) ? 'checked' : '') ?>
                            ><?= h($label) ?>
                        </label>
                    <?php endforeach; ?>
                        <script>(function() {
                            // 全てのフィールド表示チェックボックスの制御
                            document.querySelector('.all_failure_reason_code').addEventListener('change', function() {
                                const checkboxes = document.querySelectorAll('[name="failure_reason_code[]"]');
                                checkboxes.forEach(cb => cb.checked = this.checked);
                            });
                            
                            document.querySelectorAll('[name="failure_reason_code[]"]').forEach(cb => {
                                cb.addEventListener('change', function() {
                                    const allCheckbox = document.querySelector('.all_failure_reason_code');
                                    const checkboxes = document.querySelectorAll('[name="failure_reason_code[]"]');
                                    allCheckbox.checked = Array.from(checkboxes).every(cb => cb.checked);
                                });
                            });

                            document.addEventListener('DOMContentLoaded', function() {
                                const allCheckbox = document.querySelector('.all_failure_reason_code');
                                const checkboxes = document.querySelectorAll('[name="failure_reason_code[]"]');
                                allCheckbox.checked = Array.from(checkboxes).every(cb => cb.checked);
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

<!-- 検索結果 -->
<div class="card">
    <div class="card-header">
        <span>検索結果</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-bordered mb-0">
            <thead class="table-dark">
                <tr>
                    <?php
                        $pageOptions = [
                            'url' => [
                                'account_id' => $this->getRequest()->getParam('account_id'),
                            ],
                        ];
                    ?>
                    <th><?= $this->Paginator->sort('login_id', 'ログインID', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('login_actor_type', 'アカウント種別', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('login_result', 'ログイン結果', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('ip_address', 'IPアドレス', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('logged_in_at', 'ログイン日時', $pageOptions) ?></th>
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
                    <?= $this->Paginator->prev('«', $pageOptions) ?>
                    <?= $this->Paginator->numbers($pageOptions) ?>
                    <?= $this->Paginator->next('»', $pageOptions) ?>
                </ul>
            </nav>
        </div>
    </div>
</div>
