<?php
use App\Domain\Mail\Entity\Mail;
use App\Domain\Mail\ValueObject as Vo;
use App\Domain\Mail\SearchCondition;

/** @var \Cake\View\View $this */
/** @var array<\App\Domain\Mail\Entity\Mail> $rows */
/** @var bool $isPrevExists */
/** @var bool $isNextExists */
/** @var array<string> $errorMessages */
/** @var array<string> $errorFields */
?>
<!-- 検索フォーム -->
<div class="card mb-3">
<?php foreach ($errorMessages as $message) { ?>
    <div class="alert alert-error-custom" onclick="this.style.display='none'">
        <?= h($message) ?>
    </div>
<?php } ?>
    <div class="card-header bg-secondary text-white">
        メール検索
    </div>
    <div class="card-body">
        <form method="get">
            <input
                type="hidden"
                name="navigation_type"
                value="<?= h(Vo\Search\NavigationType::FIRST) ?>"
            >
            <input
                type="hidden"
                name="limit"
                value="<?= h($this->getRequest()->getQuery('limit') ?? SearchCondition::DEFAULT_LIMIT) ?>"
            >
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">送信予定日時（自）</label>
                    <input
                        type="datetime-local"
                        name="send_scheduled_at_from"
                        class="form-control"
                        step="1"
                        min="1970-01-01T00:00:00"
                        max="2999-12-31T23:59:59"
                        value="<?= h($this->getRequest()->getQuery('send_scheduled_at_from')) ?>"
                        required
                    >
                </div>
                <div class="col-md-6">
                    <label class="form-label">送信予定日時（至）</label>
                    <input
                        type="datetime-local"
                        name="send_scheduled_at_to"
                        class="form-control"
                        step="1"
                        min="1970-01-01T00:00:00"
                        max="2999-12-31T23:59:59"
                        value="<?= h($this->getRequest()->getQuery('send_scheduled_at_to')) ?>"
                        required
                    >
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label">送信ステータス</label>
                    <select name="send_status" class="form-select">
                        <option value="">-- 指定なし --</option>
                        <option value="<?= h(Vo\SendStatus::WAITING) ?>" <?= $this->getRequest()->getQuery('send_status') === Vo\SendStatus::WAITING ? 'selected' : '' ?>><?= h(Vo\SendStatus::WAITING) ?></option>
                        <option value="<?= h(Vo\SendStatus::SENT) ?>" <?= $this->getRequest()->getQuery('send_status') === Vo\SendStatus::SENT ? 'selected' : '' ?>><?= h(Vo\SendStatus::SENT) ?></option>
                        <option value="<?= h(Vo\SendStatus::FAILED) ?>" <?= $this->getRequest()->getQuery('send_status') === Vo\SendStatus::FAILED ? 'selected' : '' ?>><?= h(Vo\SendStatus::FAILED) ?></option>
                        <option value="<?= h(Vo\SendStatus::RECEIVED) ?>" <?= $this->getRequest()->getQuery('send_status') === Vo\SendStatus::RECEIVED ? 'selected' : '' ?>><?= h(Vo\SendStatus::RECEIVED) ?></option>
                        <option value="<?= h(Vo\SendStatus::BOUNCED) ?>" <?= $this->getRequest()->getQuery('send_status') === Vo\SendStatus::BOUNCED ? 'selected' : '' ?>><?= h(Vo\SendStatus::BOUNCED) ?></option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">関連データキー</label>
                    <input
                        type="text"
                        name="related_data_key"
                        class="form-control"
                        value="<?= h($this->getRequest()->getQuery('related_data_key')) ?>"
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
                    <div class="form-text">全文検索（FULLTEXT）で検索します。</div>
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
                    <th>ID</th>
                    <th>送信ステータス</th>
                    <th>送信予定日時</th>
                    <th>件名</th>
                    <th>宛先</th>
                    <th>関連データキー</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($rows) === 0): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">データがありません</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <?php /** @var \App\Domain\Mail\Entity\Mail $row */ ?>
                <tr>
                    <td><?= h($row->id()->toStringOrNull() ?? '') ?></td>
                    <td>
                        <?php if ($row->sendStatus()->toString() === Vo\SendStatus::WAITING) { ?>
                            <span class="badge bg-secondary"><?= h(Vo\SendStatus::WAITING) ?></span>
                        <?php } elseif ($row->sendStatus()->toString() === Vo\SendStatus::SENT) { ?>
                            <span class="badge bg-success"><?= h(Vo\SendStatus::SENT) ?></span>
                        <?php } elseif ($row->sendStatus()->toString() === Vo\SendStatus::FAILED) { ?>
                            <span class="badge bg-danger"><?= h(Vo\SendStatus::FAILED) ?></span>
                        <?php } elseif ($row->sendStatus()->toString() === Vo\SendStatus::RECEIVED) { ?>
                            <span class="badge bg-primary"><?= h(Vo\SendStatus::RECEIVED) ?></span>
                        <?php } elseif ($row->sendStatus()->toString() === Vo\SendStatus::BOUNCED) { ?>
                            <span class="badge bg-warning text-dark"><?= h(Vo\SendStatus::BOUNCED) ?></span>
                        <?php } else { ?>
                            <?= h($row->sendStatus()->toString()) ?>
                        <?php } ?>
                    </td>
                    <td class="text-nowrap"><?= h($row->sendScheduledAt()->format('Y/m/d H:i:s') ?? '') ?></td>
                    <td class="text-break"><?= h($row->title()->toStringOrNull() ?? '') ?></td>
                    <td class="text-break"><?= h($row->mailTo()->toStringOrNull() ?? '') ?></td>
                    <td class="text-break"><?= h($row->relatedDataKey()->toStringOrNull() ?? '') ?></td>
                    <td class="text-nowrap">
                        <a href="<?= $this->Url->build([
                            'prefix' => 'Admin/MailManage',
                            'controller' => 'Detail',
                            'action' => 'index',
                            'account_id' => $this->getRequest()->getParam('account_id'),
                            'mail_id' => $row->id()->toStringOrNull(),
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>" class="btn btn-info btn-sm">詳細</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <?php if ($isPrevExists): ?>
                    <a href="<?= $this->Url->build([
                        'prefix' => 'Admin/MailManage',
                        'controller' => 'Search',
                        'action' => 'index',
                        'account_id' => $this->getRequest()->getParam('account_id'),
                        '?' => array_merge(
                            array_diff_key(
                                (array)$this->getRequest()->getQuery(),
                                array_flip(['navigation_type', 'search_key']),
                            ), [
                                'navigation_type' => Vo\Search\NavigationType::FIRST,
                            ],
                        ),
                    ]) ?>" class="btn btn-outline-secondary btn-sm"><< 最初へ</a>
                <?php endif ?>
                <?php if ($isPrevExists): ?>
                    <a href="<?= $this->Url->build([
                        'prefix' => 'Admin/MailManage',
                        'controller' => 'Search',
                        'action' => 'index',
                        'account_id' => $this->getRequest()->getParam('account_id'),
                        '?' => array_merge(
                            array_diff_key(
                                (array)$this->getRequest()->getQuery(),
                                array_flip(['navigation_type', 'search_key']),
                            ), [
                                'navigation_type' => Vo\Search\NavigationType::PREV,
                                'search_key' => $rows[0]->id()->toStringOrNull(),
                            ],
                        ),
                    ]) ?>" class="btn btn-outline-secondary btn-sm">< 前へ</a>
                <?php endif ?>
                <?php if ($isNextExists): ?>
                    <a href="<?= $this->Url->build([
                        'prefix' => 'Admin/MailManage',
                        'controller' => 'Search',
                        'action' => 'index',
                        'account_id' => $this->getRequest()->getParam('account_id'),
                        '?' => array_merge(
                            array_diff_key(
                                (array)$this->getRequest()->getQuery(),
                                array_flip(['navigation_type', 'search_key']),
                            ), [
                                'navigation_type' => Vo\Search\NavigationType::NEXT,
                                'search_key' => $rows[count($rows) - 1]->id()->toStringOrNull(),
                            ],
                        ),
                    ]) ?>" class="btn btn-outline-secondary btn-sm">次へ ></a>
                <?php endif ?>
                <?php if ($isNextExists): ?>
                    <a href="<?= $this->Url->build([
                        'prefix' => 'Admin/MailManage',
                        'controller' => 'Search',
                        'action' => 'index',
                        'account_id' => $this->getRequest()->getParam('account_id'),
                        '?' => array_merge(
                            array_diff_key(
                                (array)$this->getRequest()->getQuery(),
                                array_flip(['navigation_type', 'search_key']),
                            ), [
                                'navigation_type' => Vo\Search\NavigationType::LAST,
                            ],
                        ),
                    ]) ?>" class="btn btn-outline-secondary btn-sm">最後へ >></a>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
