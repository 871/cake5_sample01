<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">ロール権限新規作成</div>
    <div class="card-body">
        <form method="post">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">コード</label>
                    <input type="text" name="code" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">名称</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">並び順</label>
                    <input type="number" name="sort" class="form-control" value="0" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">説明</label>
                    <input type="text" name="description" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">有効状態</label>
                    <select name="is_active" class="form-select">
                        <option value="1">有効</option>
                        <option value="0">無効</option>
                    </select>
                </div>
            </div>
            <div class="text-center mt-4">
                <?= $this->Form->button('作成する', ['class' => 'btn btn-primary px-5']) ?>
            </div>
        </form>
    </div>
</div>
