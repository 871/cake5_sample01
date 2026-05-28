<?php
declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Security\Auth\UserTokenService;
use App\Service\Controller\User\Logout as CtlService;
use Cake\Event\EventInterface;
use Cake\Http\Exception\MethodNotAllowedException;
use DateTimeImmutable;

class LogoutController extends AppController
{
    /**
     * @var \App\Service\Controller\User\Logout
     */
    private CtlService $ctlService;

    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('user_main');
        $this->ctlService = new CtlService(
            datetime: new DateTimeImmutable(),
            request: $this->request,
            authContext: AuthContextResolver::resolve($this->request),
        );
    }

    /**
     * @return void
     */
    public function index(): void
    {
        throw new MethodNotAllowedException();
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function indexPost()
    {
        $response = $this->redirect([
            'prefix' => 'User',
            'controller' => 'Login',
            'action' => 'index',
        ]);
        $response = (new UserTokenService())->withCookieHeaders(
            $response,
            $this->ctlService->logout(),
        );
        $this->Flash->error(__('ログアウトしました。'));

        return $response;
    }
}
