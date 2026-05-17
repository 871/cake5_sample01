<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant\RolePermission;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\AdminGrant\RolePermission\Detail as CtlService;
use Cake\Event\EventInterface;
use DateTimeImmutable;

class DetailController extends AppController
{
    private CtlService $ctlService;

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

    public function index()
    {
        $id = (string)$this->request->getParam('grant_role_permission_id');
        $this->set([
            'entity' => $this->ctlService->read($id),
        ]);

        return $this->render('/Admin/AdminGrant/role_permission_detail');
    }
}
