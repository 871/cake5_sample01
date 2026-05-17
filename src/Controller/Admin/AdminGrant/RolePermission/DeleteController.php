<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant\RolePermission;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\AdminGrant\RolePermission\Delete as CtlService;
use Cake\Event\EventInterface;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Log\Log;
use DateTimeImmutable;
use Throwable;

class DeleteController extends AppController
{
    /**
     * @var \App\Service\Controller\Admin\AdminGrant\RolePermission\Delete
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
        throw new MethodNotAllowedException();
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function indexPost()
    {
        $id = (string)$this->request->getParam('grant_role_permission_id');

        try {
            $this->ctlService->delete($id);
            $this->Flash->success('ロール権限を削除しました。');
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            $this->Flash->error('ロール権限の削除に失敗しました。');
        }

        return $this->redirect([
            'controller' => 'RolePermission/Search',
            'action' => 'index',
            'account_id' => $this->request->getParam('account_id'),
            '?' => $this->request->getQuery(),
        ]);
    }
}
