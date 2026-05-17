<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant\RolePermission;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\AdminGrant\RolePermission\Update as CtlService;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use DateTimeImmutable;
use Throwable;

class UpdateController extends AppController
{
    /**
     * @var \App\Service\Controller\Admin\AdminGrant\RolePermission\Update
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
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $grantRoleId = (string)$this->request->getParam('grant_role_id');
        $this->set([
            'grantRoleOptions' => $this->ctlService->getGrantRoleOptions(),
            'grantPermissionOptions' => $this->ctlService->getGrantPermissionOptions(),
            'selectedPermissionIds' => $this->ctlService->getSelectedPermissionIds($grantRoleId),
            'isEdit' => true,
            'targetGrantRoleId' => $grantRoleId,
        ]);

        return $this->render('/Admin/AdminGrant/role_permission_form');
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function indexPost()
    {
        $grantRoleId = (string)$this->request->getParam('grant_role_id');

        try {
            $this->ctlService->update($grantRoleId);
            $this->Flash->success('ロール権限を更新しました。');

            return $this->redirect([
                'controller' => 'RolePermission/Search',
                'action' => 'index',
                'account_id' => $this->request->getParam('account_id'),
                '?' => $this->request->getQuery(),
            ]);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            $this->Flash->error('ロール権限の更新に失敗しました。');
        }

        return $this->redirect([
            'controller' => 'RolePermission/Update',
            'action' => 'index',
            'account_id' => $this->request->getParam('account_id'),
            'grant_role_id' => $grantRoleId,
            '?' => $this->request->getQuery(),
        ]);
    }
}
