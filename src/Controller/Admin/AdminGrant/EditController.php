<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\AdminGrant\Edit as CtlService;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use DateTimeImmutable;
use Throwable;

class EditController extends AppController
{
    /**
     * @var \App\Service\Controller\Admin\AdminGrant\Edit
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
        $adminAccountId = (string)$this->request->getParam('admin_account_id');
        $this->set([
            'adminAccountId' => $adminAccountId,
            'grantRoleOptions' => $this->ctlService->getGrantRoleOptions(),
            'grantPermissionOptions' => $this->ctlService->getGrantPermissionOptions(),
            'grantedRoleIds' => $this->ctlService->getGrantedRoleIds($adminAccountId),
            'grantedPermissionIds' => $this->ctlService->getGrantedPermissionIds($adminAccountId),
        ]);

        return $this->render('/Admin/AdminGrant/account_permission_edit');
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function indexPost()
    {
        $adminAccountId = (string)$this->request->getParam('admin_account_id');

        try {
            $this->ctlService->save($adminAccountId);
            $this->Flash->success('管理者権限を更新しました。');

            return $this->redirect([
                'controller' => 'Detail',
                'action' => 'index',
                'account_id' => $this->request->getParam('account_id'),
                'admin_account_id' => $adminAccountId,
                '?' => $this->request->getQuery(),
            ]);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            $this->Flash->error('管理者権限の更新に失敗しました。');
        }

        return $this->redirect([
            'controller' => 'Edit',
            'action' => 'index',
            'account_id' => $this->request->getParam('account_id'),
            'admin_account_id' => $adminAccountId,
            '?' => $this->request->getQuery(),
        ]);
    }
}
