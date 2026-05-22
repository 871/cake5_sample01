<?php
/** @var \App\Domain\Admin\AdminGrant\Entity\GrantRole $entity */
?>
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">ロール権限更新</div>
    <div class="card-body">
        <form method="post">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">ID</label>
                    <input type="text" class="form-control" value="<?= h($entity->grantRoleId()->toString()) ?>" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label">コード</label>
                    <input type="text" name="code" class="form-control" value="<?= h($entity->code()->toString()) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">名称</label>
                    <input type="text" name="name" class="form-control" value="<?= h($entity->name()->toString()) ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">並び順</label>
                    <input type="number" name="sort" class="form-control" value="<?= h($entity->sort()->toString()) ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">有効状態</label>
                    <select name="is_active" class="form-select">
                        <option value="1" <?= $entity->isActive()->toInt() === 1 ? 'selected' : '' ?>>有効</option>
                        <option value="0" <?= $entity->isActive()->toInt() === 0 ? 'selected' : '' ?>>無効</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">説明</label>
                    <input type="text" name="description" class="form-control" value="<?= h($entity->description()->toString()) ?>">
                </div>
            </div>
            <div class="text-center mt-4">
                <?= $this->Form->button('更新する', ['class' => 'btn btn-primary px-5']) ?>
            </div>
        </form>
    </div>
</div>
