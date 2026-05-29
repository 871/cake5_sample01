<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant\Role;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Application\Controller\Admin\AdminGrant\Role\Detail as CtlService;
use Cake\Event\EventInterface;
use DateTimeImmutable;

class DetailController extends AppController
{
    /**
     * @var \App\Application\Controller\Admin\AdminGrant\Role\Detail
     */
    private CtlService $ctlService;

    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
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
        $this->set([
            'entity' => $this->ctlService->getDomainEntity(),
            'grantPermissionOptions' => $this->ctlService->getGrantPermissionOptions(),
        ]);

        return $this->render('/Admin/AdminGrant/Role/detail');
    }
}
