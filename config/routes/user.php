<?php

use App\Middleware\User\UserAuthMiddleware;
use App\Middleware\User\PageAccessLogMiddleware;
use Cake\Routing\RouteBuilder;

/** @var \Cake\Routing\RouteBuilder $builder */
$builder->prefix('User', ['path' => '/us'], static function (RouteBuilder $builder) {
    // エラー（未ログイン）
    $builder->get('/error', ['controller' => 'Error', 'action' => 'index']);
    $builder->get('/error/{message_id}', ['controller' => 'Error', 'action' => 'index']);
    // ログイン
    $builder->get('/login', ['controller' => 'Login', 'action' => 'index']);
    $builder->post('/login', ['controller' => 'Login', 'action' => 'indexPost']);
    // ログイン済ルート
    $builder->registerMiddleware('userAuth', new UserAuthMiddleware());
    $builder->scope('/{account_id}', static function (RouteBuilder $builder) {
        // ログアウト Memo: 認証ミドルウェアより前に置かないとログアウト後のリダイレクトで不備が出るため注意
        $builder->get('/logout', ['controller' => 'Logout', 'action' => 'index']);
        $builder->post('/logout', ['controller' => 'Logout', 'action' => 'indexPost']);
        // 認証チェック
        $builder->registerMiddleware('pageAccessLog', new PageAccessLogMiddleware());
        $builder->applyMiddleware('userAuth', 'pageAccessLog');
        // エラー
        $builder->get('/error', ['controller' => 'Error', 'action' => 'index']);
        $builder->get('/error/{message_id}', ['controller' => 'Error', 'action' => 'index']);
        // TOP
        $builder->get('/', ['controller' => 'Top', 'action' => 'index']);

        
    });
});
