<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant\RolePermission;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\AdminGrant\RolePermission\Delete as CtlService;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use DateTimeImmutable;
use Throwable;

class DeleteController extends AppController
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

        if ($this->request->is(['post', 'delete'])) {
            try {
                $this->ctlService->delete($id);
                $this->Flash->success('ロール権限を削除しました。');
            } catch (Throwable $e) {
                Log::error($e->getMessage());
                $this->Flash->error('ロール権限の削除に失敗しました。');
            }
        }

        return $this->redirect([
            'controller' => 'RolePermission/Search',
            'action' => 'index',
            'account_id' => $this->request->getParam('account_id'),
            '?' => $this->request->getQueryParams(),
        ]);
    }
}
