<?php
declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AppController;
use App\Exception\AuthException;
use App\Security\Auth\AuthContextResolver;
use App\Security\Auth\UserTokenService;
use App\Security\Input\StrictCast;
use App\Service\Controller\User\Login as CtlService;
use Cake\Event\EventInterface;
use DateTimeImmutable;

class LoginController extends AppController
{
    /**
     * @var \App\Service\Controller\User\Login
     */
    private CtlService $ctlService;

    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('user_login');
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
        return $this->render('/User/login');
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function indexPost()
    {
        try {
            $redirect = $this->ctlService
                ->login(
                    login_id: StrictCast::toString($this->request->getData('email')),
                    password: StrictCast::toString($this->request->getData('password')),
                )
                ->recordLoginSuccess()
                ->getRedirect();
            /** @var \Cake\Http\Response $response */
            $response = $this->redirect($redirect);

            return (new UserTokenService())->withCookieHeaders(
                $response,
                $this->ctlService->getSetCookieHeaders(),
            );
        } catch (AuthException $e) {
            sleep(3);
            $this->Flash->error($e->getMessage());

            return $this->render('/User/login');
        }
    }
}
