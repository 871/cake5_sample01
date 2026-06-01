<?php
declare(strict_types=1);

namespace App\Controller\Admin\UserGrant;

use App\Application\Controller\Admin\UserGrant\Detail as CtlService;
use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Security\Input\StrictCast;
use Cake\Event\EventInterface;
use DateTimeImmutable;

class DetailController extends AppController
{
    /**
     * @var \App\Application\Controller\Admin\UserGrant\Detail
     */
    private CtlService $ctlService;

    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->ctlService = new CtlService(
            datetime: new DateTimeImmutable(),
            request: $this->request,
            authContext: AuthContextResolver::resolve($this->request),
        );
        $this->viewBuilder()->setLayout('admin_main');
    }

    /**
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $this->set([
            'userAccountGrant' => $this->ctlService->getUserAccountGrant(
                userAccountId: StrictCast::toString($this->request->getParam('user_account_id')),
            ),
            'grantPermissions' => $this->ctlService->getGrantPermissions(),
        ]);

        return $this->render('/Admin/UserGrant/detail');
    }
}
