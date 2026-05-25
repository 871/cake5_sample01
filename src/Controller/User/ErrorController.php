<?php
declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Psr\Http\Message\ResponseInterface;

class ErrorController extends AppController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('user_login');
    }

    public function index(): ResponseInterface
    {
        return $this->render('/User/error');
    }
}
