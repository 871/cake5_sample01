<?php


use App\Middleware\Admin\AdminAuthMiddleware;
use App\Middleware\Admin\PageAccessLogMiddleware;
use Cake\Routing\RouteBuilder;

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
    $builder->scope('/{account_id}', static function (RouteBuilder $builder) {
        // ログアウト Memo: 認証ミドルウェアより前に置かないとログアウト後のリダイレクトで不備が出るため注意
        $builder->get('/logout', ['controller' => 'Logout', 'action' => 'index']);
        $builder->post('/logout', ['controller' => 'Logout', 'action' => 'indexPost']);
        // 認証チェック
        $builder->applyMiddleware('adminAuth', 'pageAccessLog');
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

        // ログ
        $builder->prefix('Log', ['path' => '/log'], static function (RouteBuilder $builder) {
            // ログイン試行ログ
            $builder->prefix('LoginLog', ['path' => '/login_log'], static function (RouteBuilder $builder) {
                // メール処理タスク
                $builder->get('/mail_task/send_waiting_mails', ['controller' => 'MailTask', 'action' => 'sendWaitingMails']);
                $builder->get('/mail_task/check_received_mails', ['controller' => 'MailTask', 'action' => 'checkReceivedMails']);
                $builder->get('/mail_task/check_bounced_mails', ['controller' => 'MailTask', 'action' => 'checkBouncedMails']);
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
