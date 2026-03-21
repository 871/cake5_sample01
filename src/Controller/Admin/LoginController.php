<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;

class LoginController extends AppController
{
    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('admin_login');
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        return $this->render('/Admin/Login/index');
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function indexPost()
    {
        return $this->render('/Admin/Login/index');
    }
}
