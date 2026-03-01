<?php


use Cake\Routing\RouteBuilder;


$builder->scope('/', function (RouteBuilder $builder) {
    
    $builder->get('/error', ['controller' => 'Error', 'action' => 'index']);
    $builder->get('/error/:message_id', ['controller' => 'Error', 'action' => 'index']);

    // 処理サンプル
    $builder->prefix('Sample', ['path' => '/sample'], static function (RouteBuilder $builder) {
        // Bake 
        $builder->connect('/my-sql-type-samples', ['controller' => 'MySqlTypeSamples', 'action' => 'index']);
        $builder->connect('/my-sql-type-samples/add', ['controller' => 'MySqlTypeSamples', 'action' => 'add']);
        $builder->connect('/my-sql-type-samples/view/*', ['controller' => 'MySqlTypeSamples', 'action' => 'view']);
        $builder->connect('/my-sql-type-samples/edit/*', ['controller' => 'MySqlTypeSamples', 'action' => 'edit']);
        $builder->connect('/my-sql-type-samples/delete/*', ['controller' => 'MySqlTypeSamples', 'action' => 'delete']);
        
        $builder->prefix('MySqlTypeSamples', ['path' => '/my_sql_type_samples'], static function (RouteBuilder $builder) {
            // 検索
            $builder->get('/', ['controller' => 'Search', 'action' => 'init']);
            $builder->get('/search', ['controller' => 'Search', 'action' => 'index']);
            // 登録
            $builder->get('/create', ['controller' => 'Create', 'action' => 'index']);
            $builder->get('/create/:process_id/input', ['controller' => 'Create', 'action' => 'input']);
            $builder->post('/create/:process_id/input', ['controller' => 'Create', 'action' => 'inputPost']);
            $builder->get('/create/:process_id/conf', ['controller' => 'Create', 'action' => 'conf']);
            $builder->post('/create/:process_id/conf', ['controller' => 'Create', 'action' => 'confPost']);
            // 複製登録
            $builder->get('/create/:my_sql_type_sample_id/copy', ['controller' => 'Create', 'action' => 'copy']);
    
        
        }); 
    });

});
