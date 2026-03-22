<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Exception\AuthException;
use App\Security\Auth\AuthContextResolver;
use App\Security\Input\StrictCast;
use App\Service\Controller\Admin\Login as CtlService;
use Cake\Event\EventInterface;
use DateTimeImmutable;

class LoginController extends AppController
{
    /**
     * @var \App\Service\Controller\Admin\Login
     */
    private CtlService $ctlService;

    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('admin_login');

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
        return $this->render('/Admin/login');
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function indexPost()
    {
        try {
            /** @var array<string, string>|string $redirect */
            $redirect = $this->ctlService
                ->login(
                    login_id: StrictCast::toString($this->request->getData('email')),
                    password: StrictCast::toString($this->request->getData('password')),
                )
                ->getRedirect();

            return $this->redirect($redirect);
        } catch (AuthException $e) {
            $this->Flash->error($e->getMessage());

            return $this->render('/Admin/login');
        }
    }
}
