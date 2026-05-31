<?php
// Memo: 静的解析ツールのチェックとFW依存のページ機能を両立させるための措置として、CakePHPのViewファイル内で直接ドメインエンティティへのマッピングを行っています。 --- IGNORE ---
use App\Infrastructure\Persistence\Cake\Admin\AdminAccountsRepository\Mapper as AdminAccountMapper;
use App\Model\Entity\Admin\AdminAccount as OrmEntity;


/** @var array $rows */
/** @var array $accountStatusOptions */
?>
<!-- 検索フォーム -->
<div class="card mb-3">
    <div class="card-header bg-secondary text-white">
        管理者アカウント検索
    </div>
    <div class="card-body">
        <form method="get">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">ID</label>
                    <input
                        type="number"
                        name="id"
                        class="form-control"
                        value="<?= h($this->getRequest()->getQuery('id')) ?>"
                        min="1"
                    >
                </div>
                <div class="col-md-4">
                    <label class="form-label">キーワード（メール/名前）</label>
                    <input
                        type="text"
                        name="keyword"
                        class="form-control"
                        value="<?= h($this->getRequest()->getQuery('keyword')) ?>"
                    >
                </div>
                <div class="col-md-3">
                    <label class="form-label">アカウントステータス</label>
                    <select name="account_status_master_id" class="form-select">
                        <option value="">（全て）</option>
                        <?php foreach ($accountStatusOptions as $option) { ?>
                            <option value="<?= h($option['value']) ?>"
                                <?= $this->getRequest()->getQuery('account_status_master_id') === $option['value'] ? 'selected' : '' ?>>
                                <?= h($option['label']) ?>
                            </option>
                        <?php } ?>
                    </select>
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
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>検索結果</span>
        <a href="<?= $this->Url->build([
            'prefix' => 'Admin/AdminAccount',
            'controller' => 'Create',
            'action' => 'index',
            'account_id' => $this->getRequest()->getParam('account_id'),
            '?' => $this->getRequest()->getQuery(),
        ]) ?>" class="btn btn-success btn-sm">新規登録</a>
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
                    <th><?= $this->Paginator->sort('id', 'ID', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('email', 'メールアドレス', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('name', '名前', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('account_status_master_id', 'ステータス', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('is_email_verified', 'メール確認', $pageOptions) ?></th>
                    <th><?= $this->Paginator->sort('password_changed_at', 'PW変更日時', $pageOptions) ?></th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($rows) === 0) { ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">データがありません</td>
                </tr>
            <?php } else { ?>
                <?php foreach (collection($rows)
                    ->map(fn(OrmEntity $e) => (new AdminAccountMapper)->toDomainEntity($e))
                    ->toList() as $row): ?>
                <tr>
                    <td><?= h($row->id()) ?></td>
                    <td><?= h($row->email()) ?></td>
                    <td><?= h($row->name()) ?></td>
                    <td><?= h($row->accountStatusMasterId()) ?></td>
                    <td><?= $row->isEmailVerified()->toInt() ? '確認済み' : '未確認' ?></td>
                    <td><?= h($row->passwordChangedAt()->format('Y/m/d H:i:s')) ?></td>
                    <td class="text-nowrap">
                        <a href="<?= $this->Url->build([
                            'prefix' => 'Admin/AdminAccount',
                            'controller' => 'Detail',
                            'action' => 'index',
                            'account_id' => $this->getRequest()->getParam('account_id'),
                            'admin_account_id' => $row->id()->toString(),
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>" class="btn btn-info btn-sm">詳細</a>
                        <a href="<?= $this->Url->build([
                            'prefix' => 'Admin/AdminAccount',
                            'controller' => 'Edit',
                            'action' => 'index',
                            'account_id' => $this->getRequest()->getParam('account_id'),
                            'admin_account_id' => $row->id()->toString(),
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>" class="btn btn-primary btn-sm">更新</a>
                        <a href="<?= $this->Url->build([
                            'prefix' => 'Admin/AdminAccount',
                            'controller' => 'Create',
                            'action' => 'copy',
                            'account_id' => $this->getRequest()->getParam('account_id'),
                            'admin_account_id' => $row->id()->toString(),
                            '?' => $this->getRequest()->getQuery(),
                        ]) ?>" class="btn btn-primary btn-sm">複製</a>
                        <?= $this->Form->postLink('削除', [
                            'prefix' => 'Admin/AdminAccount',
                            'controller' => 'Delete',
                            'action' => 'index',
                            'account_id' => $this->getRequest()->getParam('account_id'),
                            'admin_account_id' => $row->id()->toString(),
                            '?' => $this->getRequest()->getQuery(),
                        ], [
                            'class' => 'btn btn-danger btn-sm',
                            'confirm' => '削除しますか？',
                        ]) ?>
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
