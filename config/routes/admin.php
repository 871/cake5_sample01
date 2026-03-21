<?php


use Cake\Routing\RouteBuilder;

$builder->prefix('Admin', ['path' => '/ad'], static function (RouteBuilder $builder) {
    // エラー（未ログイン）
    $builder->get('/error', ['controller' => 'Error', 'action' => 'index']);
    $builder->get('/error/{message_id}', ['controller' => 'Error', 'action' => 'index']);
    // ログイン
    $builder->get('/login', ['controller' => 'Login', 'action' => 'index']);
    $builder->post('/login', ['controller' => 'Login', 'action' => 'indexPost']);
    // ログイン済ルート
    $builder->scope('/{account_id}', static function (RouteBuilder $builder) {
        // エラー
        $builder->get('/error', ['controller' => 'Error', 'action' => 'index']);
        $builder->get('/error/{message_id}', ['controller' => 'Error', 'action' => 'index']);
        // TOP
        $builder->get('/', ['controller' => 'Main', 'action' => 'index']);
        // ログアウト
        $builder->get('/logout', ['controller' => 'Logout', 'action' => 'index']);
        $builder->post('/logout', ['controller' => 'Logout', 'action' => 'indexPost']);


    });
});
