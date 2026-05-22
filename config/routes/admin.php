<?php


use App\Middleware\Admin\AdminAuthMiddleware;
use App\Middleware\Admin\AdminGrantMiddleware;
use App\Middleware\Admin\PageAccessLogMiddleware;
use Cake\Routing\RouteBuilder;

/** @var \Cake\Routing\RouteBuilder $builder */
$builder->prefix('Admin', ['path' => '/ad'], static function (RouteBuilder $builder) {

    // エラー（未ログイン）
    $builder->get('/error', ['controller' => 'Error', 'action' => 'index']);
    $builder->get('/error/{message_id}', ['controller' => 'Error', 'action' => 'index']);
    // ログイン
    $builder->get('/login', ['controller' => 'Login', 'action' => 'index']);
    $builder->post('/login', ['controller' => 'Login', 'action' => 'indexPost']);
    // ログイン済ルート
    $builder->registerMiddleware('adminAuth', new AdminAuthMiddleware());
    $builder->registerMiddleware('pageAccessLog', new PageAccessLogMiddleware());
    $builder->registerMiddleware('adminGrant', new AdminGrantMiddleware());
    $builder->scope('/{account_id}', static function (RouteBuilder $builder) {
        // ログアウト Memo: 認証ミドルウェアより前に置かないとログアウト後のリダイレクトで不備が出るため注意
        $builder->get('/logout', ['controller' => 'Logout', 'action' => 'index']);
        $builder->post('/logout', ['controller' => 'Logout', 'action' => 'indexPost']);
        // 認証チェック
        $builder->applyMiddleware('adminAuth', 'pageAccessLog', 'adminGrant');
        // エラー
        $builder->get('/error', ['controller' => 'Error', 'action' => 'index']);
        $builder->get('/error/{message_id}', ['controller' => 'Error', 'action' => 'index']);
        // TOP
        $builder->get('/', ['controller' => 'Top', 'action' => 'index']);
        $builder->get('/error_test', ['controller' => 'Top', 'action' => 'errorTest']);

        // メール管理
        $builder->prefix('MailManage', ['path' => '/mail_manage'], static function (RouteBuilder $builder) {
            // メールサーバ接続確認
            $builder->get('/check_mail_server', ['controller' => 'CheckMailServer', 'action' => 'index']);
            $builder->get('/check_mail_server/sent_smtp', ['controller' => 'CheckMailServer', 'action' => 'sentSmtp']);
            $builder->get('/check_mail_server/received_check_imap', ['controller' => 'CheckMailServer', 'action' => 'receivedCheckImap']);
            $builder->get('/check_mail_server/return_path_imap', ['controller' => 'CheckMailServer', 'action' => 'returnPathImap']);
            // メール処理タスク
            $builder->get('/mail_task/send_waiting_mails', ['controller' => 'MailTask', 'action' => 'sendWaitingMails']);
            $builder->get('/mail_task/check_received_mails', ['controller' => 'MailTask', 'action' => 'checkReceivedMails']);
            $builder->get('/mail_task/check_bounced_mails', ['controller' => 'MailTask', 'action' => 'checkBouncedMails']);
            // 登録
            $builder->get('/create', ['controller' => 'Create', 'action' => 'index']);
            $builder->get('/create/{process_id}/input', ['controller' => 'Create', 'action' => 'input']);
            $builder->post('/create/{process_id}/input', ['controller' => 'Create', 'action' => 'inputPost']);
            $builder->get('/create/{process_id}/conf', ['controller' => 'Create', 'action' => 'conf']);
            $builder->post('/create/{process_id}/conf', ['controller' => 'Create', 'action' => 'confPost']);
            // 検索
            $builder->get('/', ['controller' => 'Search', 'action' => 'init']);
            $builder->get('/search', ['controller' => 'Search', 'action' => 'index']);
            // 詳細
            $builder->get('/detail/{mail_id}', ['controller' => 'Detail', 'action' => 'index']);
        });

        // 管理者アカウント管理
        $builder->prefix('AdminAccount', ['path' => '/admin_account'], static function (RouteBuilder $builder) {
            // 検索
            $builder->get('/', ['controller' => 'Search', 'action' => 'init']);
            $builder->get('/search', ['controller' => 'Search', 'action' => 'index']);
            // 詳細
            $builder->get('/detail/{admin_account_id}', ['controller' => 'Detail', 'action' => 'index']);
            // 登録
            $builder->get('/create', ['controller' => 'Create', 'action' => 'index']);
            $builder->get('/create/{process_id}/input', ['controller' => 'Create', 'action' => 'input']);
            $builder->post('/create/{process_id}/input', ['controller' => 'Create', 'action' => 'inputPost']);
            $builder->get('/create/{process_id}/conf', ['controller' => 'Create', 'action' => 'conf']);
            $builder->post('/create/{process_id}/conf', ['controller' => 'Create', 'action' => 'confPost']);
            // 複製登録
            $builder->get('/create/{admin_account_id}/copy', ['controller' => 'Create', 'action' => 'copy']);
            // 更新
            $builder->get('/edit/{admin_account_id}', ['controller' => 'Edit', 'action' => 'index']);
            $builder->get('/edit/{process_id}/input', ['controller' => 'Edit', 'action' => 'input']);
            $builder->post('/edit/{process_id}/input', ['controller' => 'Edit', 'action' => 'inputPost']);
            $builder->get('/edit/{process_id}/conf', ['controller' => 'Edit', 'action' => 'conf']);
            $builder->post('/edit/{process_id}/conf', ['controller' => 'Edit', 'action' => 'confPost']);
            // 削除
            $builder->get('/delete/{admin_account_id}', ['controller' => 'Delete', 'action' => 'index']);
            $builder->post('/delete/{admin_account_id}', ['controller' => 'Delete', 'action' => 'indexPost']);
        });

        // 管理者権限管理
        $builder->prefix('AdminGrant', ['path' => '/admin_grant'], static function (RouteBuilder $builder) {
            // 管理者権限
            $builder->get('/', ['controller' => 'Search', 'action' => 'init']);
            $builder->get('/search', ['controller' => 'Search', 'action' => 'index']);
            $builder->get('/detail/{admin_account_id}', ['controller' => 'Detail', 'action' => 'index']);
            $builder->get('/edit/{admin_account_id}', ['controller' => 'Edit', 'action' => 'index']);
            $builder->get('/edit/{process_id}/input', ['controller' => 'Edit', 'action' => 'input']);
            $builder->post('/edit/{process_id}/input', ['controller' => 'Edit', 'action' => 'inputPost']);
            $builder->get('/edit/{process_id}/conf', ['controller' => 'Edit', 'action' => 'conf']);
            $builder->post('/edit/{process_id}/conf', ['controller' => 'Edit', 'action' => 'confPost']);
            // ロール権限
            $builder->prefix('Role', ['path' => '/role'], static function (RouteBuilder $builder) {
                $builder->get('/', ['controller' => 'Search', 'action' => 'init']);
                $builder->get('/search', ['controller' => 'Search', 'action' => 'index']);
                $builder->get('/create', ['controller' => 'Create', 'action' => 'index']);
                $builder->get('/create/{process_id}/input', ['controller' => 'Create', 'action' => 'input']);
                $builder->post('/create/{process_id}/input', ['controller' => 'Create', 'action' => 'inputPost']);
                $builder->get('/create/{process_id}/conf', ['controller' => 'Create', 'action' => 'conf']);
                $builder->post('/create/{process_id}/conf', ['controller' => 'Create', 'action' => 'confPost']);
                $builder->get('/detail/{grant_role_id}', ['controller' => 'Detail', 'action' => 'index']);
                $builder->get('/edit/{grant_role_id}', ['controller' => 'Edit', 'action' => 'index']);
                $builder->get('/edit/{process_id}/input', ['controller' => 'Edit', 'action' => 'input']);
                $builder->post('/edit/{process_id}/input', ['controller' => 'Edit', 'action' => 'inputPost']);
                $builder->get('/edit/{process_id}/conf', ['controller' => 'Edit', 'action' => 'conf']);
                $builder->post('/edit/{process_id}/conf', ['controller' => 'Edit', 'action' => 'confPost']);
                $builder->get('/delete/{grant_role_id}', ['controller' => 'Delete', 'action' => 'index']);
                $builder->post('/delete/{grant_role_id}', ['controller' => 'Delete', 'action' => 'indexPost']);
            });
        });

        // ログ
        $builder->prefix('Log', ['path' => '/log'], static function (RouteBuilder $builder) {
            // ログイン試行ログ
            $builder->prefix('LoginLog', ['path' => '/login_log'], static function (RouteBuilder $builder) {
                // 検索
                $builder->get('/', ['controller' => 'Search', 'action' => 'init']);
                $builder->get('/search', ['controller' => 'Search', 'action' => 'index']);
                // 詳細
                $builder->get('/detail/{login_log_id}', ['controller' => 'Detail', 'action' => 'index']);
            });
            // ページアクセスログ
            $builder->prefix('PageAccessLog', ['path' => '/page_access_log'], static function (RouteBuilder $builder) {
                // 検索
                $builder->get('/', ['controller' => 'Search', 'action' => 'init']);
                $builder->get('/search', ['controller' => 'Search', 'action' => 'index']);
            });
        });
    });
});
