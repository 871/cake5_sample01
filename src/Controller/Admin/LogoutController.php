<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\Logout as CtlService;
use Cake\Event\EventInterface;
use Cake\Http\Exception\MethodNotAllowedException;
use DateTimeImmutable;

class LogoutController extends AppController
{
    /**
     * @var \App\Service\Controller\Admin\Logout
     */
    private CtlService $ctlService;

    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('admin_main');

        $this->ctlService = new CtlService(
            datetime: new DateTimeImmutable(),
            request: $this->request,
            authContext: AuthContextResolver::resolve($this->request),
        );
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        throw new MethodNotAllowedException();
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function indexPost()
    {
        $this->ctlService->logout();

        $this->Flash->error(__('ログアウトしました。'));

        return $this->redirect([
            'prefix' => 'Admin',
            'controller' => 'Login',
            'action' => 'index',
        ]);
    }
}
