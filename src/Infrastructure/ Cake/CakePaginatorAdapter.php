<?php
declare(strict_types=1);

namespace App\Infrastructure\Cake;

use \Cake\Datasource\RepositoryInterface;
use \Cake\Datasource\QueryInterface;
use \Cake\Datasource\Paging\PaginatedInterface;
use App\Domain\Sample\Sample\MySqlTypeSamples\Paginator;


interface CakePaginatorAdapter extends Paginator
{
    public function paginate(
        RepositoryInterface|QueryInterface|string|null $object = null, 
        array $settings = []
    ) : PaginatedInterface;
}