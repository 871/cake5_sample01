<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant\RolePermission;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\AdminGrant\RolePermission\Create as CtlService;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use DateTimeImmutable;
use Throwable;

class CreateController extends AppController
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
        if ($this->request->is('post')) {
            try {
                $this->ctlService->createFromRequest();
                $this->Flash->success('ロール権限を作成しました。');

                return $this->redirect([
                    'controller' => 'RolePermission/Search',
                    'action' => 'index',
                    'account_id' => $this->request->getParam('account_id'),
                    '?' => $this->request->getQueryParams(),
                ]);
            } catch (Throwable $e) {
                Log::error($e->getMessage());
                $this->Flash->error('ロール権限の作成に失敗しました。');
            }
        }

        $this->set([
            'grantRoleOptions' => $this->ctlService->getGrantRoleOptions(),
            'grantPermissionOptions' => $this->ctlService->getGrantPermissionOptions(),
            'selectedPermissionIds' => [],
            'isEdit' => false,
            'targetGrantRoleId' => null,
        ]);

        return $this->render('/Admin/AdminGrant/role_permission_form');
    }
}
