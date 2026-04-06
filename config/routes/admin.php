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
