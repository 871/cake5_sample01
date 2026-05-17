<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\AdminGrant\Detail as CtlService;
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
        $adminAccountId = (string)$this->request->getParam('admin_account_id');
        $this->set([
            'adminAccountId' => $adminAccountId,
            'permissions' => $this->ctlService->getAccountPermissions($adminAccountId),
        ]);

        return $this->render('/Admin/AdminGrant/account_permission_detail');
    }
}
