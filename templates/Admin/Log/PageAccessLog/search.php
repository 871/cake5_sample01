<?php
use App\Domain\Log\PageAccessLogs\Entity\PageAccessLog;
use App\Domain\Log\PageAccessLogs\ValueObject as Vo;

/* @var \Cake\View\View $this */
/* @var array<\App\Domain\Log\PageAccessLogs\Entity\PageAccessLog> $rows */
/* @var array<string, string>|null $prevCursor */
/* @var array<string, string>|null $nextCursor */
?>
<!-- 検索フォーム -->
<div class="card mb-3">
    <div class="card-header bg-secondary text-white">
        ページアクセスログ検索
    </div>
    <div class="card-body">
        <form method="get">
            <input 
                type="hidden" 
                name="navigation_type" 
                value="<?= h($this->getRequest()->getQuery('navigation_type')) ?? Vo\Search\NavigationType::FIRST ?>"
            >
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
                        required
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
                        required
                    >
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label">アカウント種別</label>
                    <select name="account_type" class="form-select">
                        <option value="">-- 指定なし --</option>
                        <option value="<?= h(Vo\AccountType::ADMIN) ?>" <?= $this->getRequest()->getQuery('account_type') === Vo\AccountType::ADMIN ? 'selected' : '' ?>>ADMIN</option>
                        <option value="<?= h(Vo\AccountType::USER) ?>" <?= $this->getRequest()->getQuery('account_type') === Vo\AccountType::USER ? 'selected' : '' ?>>USER</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">アカウントID</label>
                    <input
                        type="number"
                        name="account_id"
                        class="form-control"
                        min="1"
                        value="<?= h($this->getRequest()->getQuery('account_id')) ?>"
                    >
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-12">
                    <label class="form-label">キーワード</label>
                    <input
                        type="text"
                        name="keyword"
                        class="form-control"
                        value="<?= h($this->getRequest()->getQuery('keyword')) ?>"
                    >
                    <div class="form-text">パス、ルート名、IPアドレス、ユーザーエージェント、アカウント名で検索します。スペース区切りでAND検索。</div>
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
                    <th>アクセス日時</th>
                    <th>アカウント種別</th>
                    <th>アカウントID</th>
                    <th>メソッド</th>
                    <th>パス</th>
                    <th>IPアドレス</th>
                    <th>ルート名</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($rows) === 0): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">データがありません</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <?php /** @var \App\Domain\Log\PageAccessLogs\Entity\PageAccessLog $row */ ?>
                <tr>
                    <td class="text-nowrap"><?= h($row->accessed()->format('Y/m/d H:i:s') ?? '') ?></td>
                    <td>
                        <?php if ($row->accountType()->toStringOrNull() === Vo\AccountType::ADMIN) { ?>
                            <span class="badge bg-primary">ADMIN</span>
                        <?php } elseif ($row->accountType()->toStringOrNull() === Vo\AccountType::USER) { ?>
                            <span class="badge bg-success">USER</span>
                        <?php } else { ?>
                            <?= h($row->accountType()->toStringOrNull() ?? '') ?>
                        <?php } ?>
                    </td>
                    <td><?= h($row->accountId()->toStringOrNull() ?? '') ?></td>
                    <td><?= h($row->method()->toStringOrNull() ?? '') ?></td>
                    <td class="text-break"><?= h($row->path()->toStringOrNull() ?? '') ?></td>
                    <td><?= h($row->ipAddress()->toStringOrNull() ?? '') ?></td>
                    <td class="text-break"><?= h($row->routeName()->toStringOrNull() ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <?php if ($prevCursor !== null): ?>
                    <a href="<?= $this->Url->build([
                        'prefix' => 'Admin/Log/PageAccessLog',
                        'controller' => 'Search',
                        'action' => 'index',
                        'account_id' => $this->getRequest()->getParam('account_id'),
                        '?' => array_merge(
                            array_diff_key(
                                (array)$this->getRequest()->getQuery(),
                                array_flip(['navigation_type', 'cursor_id', 'cursor_accessed']),
                            ),
                            $prevCursor,
                        ),
                    ]) ?>" class="btn btn-outline-secondary btn-sm">« 前へ（新しい）</a>
                <?php endif ?>
            </div>
            <div>
                <?php if ($nextCursor !== null): ?>
                    <a href="<?= $this->Url->build([
                        'prefix' => 'Admin/Log/PageAccessLog',
                        'controller' => 'Search',
                        'action' => 'index',
                        'account_id' => $this->getRequest()->getParam('account_id'),
                        '?' => array_merge(
                            array_diff_key(
                                (array)$this->getRequest()->getQuery(),
                                array_flip(['navigation_type', 'cursor_id', 'cursor_accessed']),
                            ),
                            $nextCursor,
                        ),
                    ]) ?>" class="btn btn-outline-secondary btn-sm">次へ（古い） »</a>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
