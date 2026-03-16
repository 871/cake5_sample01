<?php


use Cake\Routing\RouteBuilder;

// TODO 未実装
$builder->prefix('User', ['path' => '/us'], static function (RouteBuilder $builder) {
    // エラー（未ログイン）
    $builder->get('/error', ['controller' => 'Error', 'action' => 'index']);
    $builder->get('/error/{message_id}', ['controller' => 'Error', 'action' => 'index']);
    // ログイン
    $builder->get('/login', ['controller' => 'Auth', 'action' => 'login']);
    $builder->post('/login', ['controller' => 'Auth', 'action' => 'loginPost']);
    // ログイン済ルート
    $builder->scope('/{account_id}', function (RouteBuilder $builder) {
        // エラー
        $builder->get('/error', ['controller' => 'Error', 'action' => 'index']);
        $builder->get('/error/{message_id}', ['controller' => 'Error', 'action' => 'index']);
        // TOP
        $builder->get('/', ['controller' => 'Main', 'action' => 'index']);
        // ログアウト
        $builder->get('/logout', ['controller' => 'Auth', 'action' => 'logout']);
        $builder->post('/logout', ['controller' => 'Auth', 'action' => 'logoutPost']);


    });
});
