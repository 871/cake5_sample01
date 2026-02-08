<?php


use Cake\Routing\RouteBuilder;


$builder->scope('/', function (RouteBuilder $builder) {
    
    $builder->get('/error', ['controller' => 'Error', 'action' => 'index']);
    $builder->get('/error/:message_id', ['controller' => 'Error', 'action' => 'index']);

    $builder->prefix('Sample', ['path' => '/sample'], static function (RouteBuilder $builder) {
        
        $builder->connect('/my-sql-type-samples', ['controller' => 'MySqlTypeSamples', 'action' => 'index']);
        $builder->connect('/my-sql-type-samples/add', ['controller' => 'MySqlTypeSamples', 'action' => 'add']);
        $builder->connect('/my-sql-type-samples/view/*', ['controller' => 'MySqlTypeSamples', 'action' => 'view']);
        $builder->connect('/my-sql-type-samples/edit/*', ['controller' => 'MySqlTypeSamples', 'action' => 'edit']);
        $builder->connect('/my-sql-type-samples/delete/*', ['controller' => 'MySqlTypeSamples', 'action' => 'delete']);
    });

});
