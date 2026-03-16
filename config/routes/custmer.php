<?php


use Cake\Routing\RouteBuilder;

// TODO 未実装
$builder->prefix('Custmer', ['path' => '/cs'], static function (RouteBuilder $builder) {
    // エラー（未ログイン）
    $builder->get('/error', ['controller' => 'Error', 'action' => 'index']);
    $builder->get('/error/{message_id}', ['controller' => 'Error', 'action' => 'index']);
    // TOP
    $builder->get('/', ['controller' => 'Main', 'action' => 'index']);
});
