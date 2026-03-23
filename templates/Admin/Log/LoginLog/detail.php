<?php
/* @var \Cake\View\View $this */
/* @var \App\Model\Entity\Log\LoginLog $entity */
?>
<div class="card shadow-sm mb-3">
    <div class="card-header bg-info text-white">
        ログイン試行ログ詳細
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th style="width:30%">ID</th>
                <td><?= h($entity->id) ?></td>
            </tr>
            <tr>
                <th>ログインID</th>
                <td><?= h($entity->login_id) ?></td>
            </tr>
            <tr>
                <th>アクター種別</th>
                <td><?= h($entity->login_actor_type) ?></td>
            </tr>
            <tr>
                <th>アカウントID</th>
                <td><?= h($entity->account_id) ?></td>
            </tr>
            <tr>
                <th>なりすまし管理者アカウントID</th>
                <td><?= h($entity->impersonator_account_id) ?></td>
            </tr>
            <tr>
                <th>ログイン結果</th>
                <td>
                    <?php if ($entity->login_result === 'SUCCESS') { ?>
                        <span class="badge bg-success">SUCCESS</span>
                    <?php } elseif ($entity->login_result === 'FAILURE') { ?>
                        <span class="badge bg-danger">FAILURE</span>
                    <?php } else { ?>
                        <?= h($entity->login_result) ?>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th>IPアドレス</th>
                <td><?= h($entity->ip_address) ?></td>
            </tr>
            <tr>
                <th>ユーザーエージェント</th>
                <td style="white-space:pre-wrap"><?= h($entity->user_agent) ?></td>
            </tr>
            <tr>
                <th>失敗理由コード</th>
                <td><?= h($entity->failure_reason_code) ?></td>
            </tr>
            <tr>
                <th>ログイン日時</th>
                <td><?= h($entity->logged_in_at?->format('Y/m/d H:i:s') ?? '----/--/-- --:--:--') ?></td>
            </tr>
            <tr>
                <th>作成日時</th>
                <td><?= h($entity->created?->format('Y/m/d H:i:s') ?? '----/--/-- --:--:--') ?></td>
            </tr>
        </table>

        <div class="text-center mt-4">
            <a href="<?= $this->Url->build([
                'prefix' => 'Admin/Log/LoginLog',
                'controller' => 'Search',
                'action' => 'index',
                'account_id' => $this->getRequest()->getParam('account_id'),
                '?' => $this->getRequest()->getQuery(),
            ]) ?>" class="btn btn-secondary px-5">戻る</a>
        </div>
    </div>
</div>
