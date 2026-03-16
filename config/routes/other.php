<?php


use Cake\Routing\RouteBuilder;


$builder->scope('/', function (RouteBuilder $builder) {
    
    $builder->get('/error', ['controller' => 'Error', 'action' => 'index']);
    $builder->get('/error/{message_id}', ['controller' => 'Error', 'action' => 'index']);

    // 管理者Bake
    $builder->prefix('AdminBake', ['path' => '/admin-bake'], static function (RouteBuilder $builder) {
        $builder->connect('/admin-accounts', ['controller' => 'AdminAccounts', 'action' => 'index']);
        $builder->connect('/admin-accounts/add', ['controller' => 'AdminAccounts', 'action' => 'add']);
        $builder->connect('/admin-accounts/view/*', ['controller' => 'AdminAccounts', 'action' => 'view']);
        $builder->connect('/admin-accounts/edit/*', ['controller' => 'AdminAccounts', 'action' => 'edit']);
        $builder->connect('/admin-accounts/delete/*', ['controller' => 'AdminAccounts', 'action' => 'delete']);
    });
    // 管理者アカウント管理
    $builder->prefix('AdminAccount', ['path' => '/admin_account'], static function (RouteBuilder $builder) {
        // 検索
        $builder->get('/', ['controller' => 'Search', 'action' => 'init']);
        $builder->get('/search', ['controller' => 'Search', 'action' => 'index']);
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

    // サンプルBake
    $builder->prefix('SampleBake', ['path' => '/sample-bake'], static function (RouteBuilder $builder) {
        // Bake 
        $builder->connect('/my-sql-type-samples', ['controller' => 'MySqlTypeSamples', 'action' => 'index']);
        $builder->connect('/my-sql-type-samples/add', ['controller' => 'MySqlTypeSamples', 'action' => 'add']);
        $builder->connect('/my-sql-type-samples/view/*', ['controller' => 'MySqlTypeSamples', 'action' => 'view']);
        $builder->connect('/my-sql-type-samples/edit/*', ['controller' => 'MySqlTypeSamples', 'action' => 'edit']);
        $builder->connect('/my-sql-type-samples/delete/*', ['controller' => 'MySqlTypeSamples', 'action' => 'delete']);
    });
    // サンプルコード
    $builder->prefix('SampleCode', ['path' => '/sample_code'], static function (RouteBuilder $builder) {
        
        $builder->prefix('MySqlTypeSamples', ['path' => '/my_sql_type_samples'], static function (RouteBuilder $builder) {
            // 検索
            $builder->get('/', ['controller' => 'Search', 'action' => 'init']);
            $builder->get('/search', ['controller' => 'Search', 'action' => 'index']);
            // 詳細
            $builder->get('/detail/{my_sql_type_sample_id}', ['controller' => 'Detail', 'action' => 'index']);
            // 登録
            $builder->get('/create', ['controller' => 'Create', 'action' => 'index']);
            $builder->get('/create/{process_id}/input', ['controller' => 'Create', 'action' => 'input']);
            $builder->post('/create/{process_id}/input', ['controller' => 'Create', 'action' => 'inputPost']);
            $builder->get('/create/{process_id}/conf', ['controller' => 'Create', 'action' => 'conf']);
            $builder->post('/create/{process_id}/conf', ['controller' => 'Create', 'action' => 'confPost']);
            // 複製登録
            $builder->get('/create/{my_sql_type_sample_id}/copy', ['controller' => 'Create', 'action' => 'copy']);
            // 更新
            $builder->get('/edit/{my_sql_type_sample_id}', ['controller' => 'Edit', 'action' => 'index']);
            $builder->get('/edit/{process_id}/input', ['controller' => 'Edit', 'action' => 'input']);
            $builder->post('/edit/{process_id}/input', ['controller' => 'Edit', 'action' => 'inputPost']);
            $builder->get('/edit/{process_id}/conf', ['controller' => 'Edit', 'action' => 'conf']);
            $builder->post('/edit/{process_id}/conf', ['controller' => 'Edit', 'action' => 'confPost']);
            // 削除
            $builder->get('/delete/{my_sql_type_sample_id}', ['controller' => 'Delete', 'action' => 'index']);
            $builder->post('/delete/{my_sql_type_sample_id}', ['controller' => 'Delete', 'action' => 'indexPost']);
        }); 
    });

});
