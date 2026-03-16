<?php

use App\Domain\Admin\AdminAccounts\ValueObject\Status;

/* @var \App\Domain\Admin\AdminAccounts\Entity\AdminAccount[] $rows */
/* @var \App\Domain\Admin\AdminRoles\Entity\AdminRole[] $roles */

$roleOptions = [];
foreach ($roles as $role) {
    $roleOptions[$role->id()->toString()] = $role->name()->toString();
}

?>
<!-- 検索フォーム -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-secondary text-white">
        検索条件
    </div>
    <div class="card-body">
        <form method="get" action="<?= $this->Url->build(['action' => 'index']) ?>">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">キーワード（ログインID / 名前 / メール）</label>
                    <input 
                        type="text" 
                        name="keyword" 
                        class="form-control"
                        value="<?= h($this->getRequest()->getQuery('keyword')) ?>"
                        placeholder="キーワード検索"
                    >
                </div>
                <div class="col-md-4">
                    <label class="form-label">権限</label>
                    <select name="role_id" class="form-select">
                        <option value="">-- すべて --</option>
                        <?php foreach ($roleOptions as $id => $name): ?>
                            <option 
                                value="<?= h($id) ?>"
                                <?= $this->getRequest()->getQuery('role_id') === $id ? 'selected' : '' ?>
                            ><?= h($name) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ステータス</label>
                    <select name="status" class="form-select">
                        <option value="">-- すべて --</option>
                        <?php foreach (Status::LABELS as $value => $label): ?>
                            <option 
                                value="<?= h((string)$value) ?>"
                                <?= $this->getRequest()->getQuery('status') === (string)$value ? 'selected' : '' ?>
                            ><?= h($label) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary px-5">検索</button>
                <a href="<?= $this->Url->build(['action' => 'init']) ?>" class="btn btn-secondary px-5">リセット</a>
            </div>
        </form>
    </div>
</div>

<!-- 検索結果 -->
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span>検索結果</span>
        <span class="badge bg-light text-dark"><?= $this->Paginator->counter('{{count}} 件 / 全 {{pages}} ページ') ?></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th><?= $this->Paginator->sort('login_id', 'ログインID') ?></th>
                        <th><?= $this->Paginator->sort('name', '名前') ?></th>
                        <th><?= $this->Paginator->sort('email', 'メールアドレス') ?></th>
                        <th><?= $this->Paginator->sort('role_id', '権限') ?></th>
                        <th><?= $this->Paginator->sort('status', 'ステータス') ?></th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php $hasRows = false; foreach ($rows as $row): $hasRows = true; ?>
                    <tr>
                        <td><?= h($row->loginId()) ?></td>
                        <td><?= h($row->name()) ?></td>
                        <td><?= h($row->email()) ?></td>
                        <td><?= h($row->roleName()) ?></td>
                        <td>
                            <span class="badge <?= $row->status()->toInt() === Status::ACTIVE ? 'bg-success' : 'bg-secondary' ?>">
                                <?= h($row->status()->toLabel()) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= $this->Url->build([
                                'controller' => 'Detail',
                                'action' => 'index',
                                'admin_account_id' => $row->id()->toString(),
                                '?' => $this->getRequest()->getQuery(),
                            ]) ?>" class="btn btn-sm btn-info text-white">詳細</a>
                            <a href="<?= $this->Url->build([
                                'controller' => 'Edit',
                                'action' => 'index',
                                'admin_account_id' => $row->id()->toString(),
                                '?' => $this->getRequest()->getQuery(),
                            ]) ?>" class="btn btn-sm btn-primary">更新</a>
                            <?= $this->Form->postLink('削除', [
                                'controller' => 'Delete',
                                'action' => 'index',
                                'admin_account_id' => $row->id()->toString(),
                                '?' => $this->getRequest()->getQuery(),
                            ], [
                                'class' => 'btn btn-sm btn-danger',
                                'confirm' => '「' . h($row->loginId()) . '」を削除しますか？'
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach ?>
                <?php if (!$hasRows): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">データが見つかりませんでした。</td>
                    </tr>
                <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <?= $this->Paginator->prev('前へ', ['class' => 'btn btn-sm btn-outline-secondary me-1']) ?>
        <?= $this->Paginator->numbers(['class' => 'btn btn-sm btn-outline-primary me-1']) ?>
        <?= $this->Paginator->next('次へ', ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
