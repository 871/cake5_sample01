<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\AdminGrant\RolePermission as CtlService;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Log\Log;
use DateTimeImmutable;
use Throwable;

class RolePermissionController extends AppController
{
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
    public function init()
    {
        $this->redirect([
            'action' => 'search',
            'account_id' => $this->request->getParam('account_id'),
            '?' => $this->ctlService->getInitParams(),
        ]);
    }

    /**
     * @return \Cake\Http\Response|null|void
     */
    public function search()
    {
        try {
            $this->set([
                'rows' => $this->paginate(
                    $this->ctlService->getSearchQuery(),
                    $this->ctlService->getPaginateSettings(),
                ),
            ]);
        } catch (NotFoundException $e) {
            Log::error('無効なページが指定されました。[message: ' . $e->getMessage() . ']');
            $this->Flash->warning('無効なページが指定されました。1ページ目を表示します。');

            return $this->redirect([
                'account_id' => $this->request->getParam('account_id'),
                '?' => array_merge((array)$this->request->getQuery(), ['page' => 1]),
            ]);
        }

        return $this->render('/Admin/AdminGrant/role_permission');
    }

    /**
     * @return \Cake\Http\Response|null|void
     */
    public function create()
    {
        if ($this->request->is('post')) {
            try {
                $this->ctlService->createFromRequest();
                $this->Flash->success('ロール権限を作成しました。');

                return $this->redirect([
                    'action' => 'search',
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

    /**
     * @return \Cake\Http\Response|null|void
     */
    public function detail()
    {
        $id = (string)$this->request->getParam('grant_role_permission_id');
        $this->set([
            'entity' => $this->ctlService->read($id),
        ]);

        return $this->render('/Admin/AdminGrant/role_permission_detail');
    }

    /**
     * @return \Cake\Http\Response|null|void
     */
    public function edit()
    {
        $grantRoleId = (string)$this->request->getParam('grant_role_id');

        if ($this->request->is(['post', 'put'])) {
            try {
                $this->ctlService->update($grantRoleId);
                $this->Flash->success('ロール権限を更新しました。');

                return $this->redirect([
                    'action' => 'search',
                    'account_id' => $this->request->getParam('account_id'),
                    '?' => $this->request->getQueryParams(),
                ]);
            } catch (Throwable $e) {
                Log::error($e->getMessage());
                $this->Flash->error('ロール権限の更新に失敗しました。');
            }
        }

        $selectedPermissionIds = collection($this->ctlService->getSearchQuery()->all())
            ->filter(fn($row) => (string)$row->grant_role_id === $grantRoleId)
            ->map(fn($row) => (string)$row->grant_permission_id)
            ->toList();

        $this->set([
            'grantRoleOptions' => $this->ctlService->getGrantRoleOptions(),
            'grantPermissionOptions' => $this->ctlService->getGrantPermissionOptions(),
            'selectedPermissionIds' => $selectedPermissionIds,
            'isEdit' => true,
            'targetGrantRoleId' => $grantRoleId,
        ]);

        return $this->render('/Admin/AdminGrant/role_permission_form');
    }

    /**
     * @return \Cake\Http\Response|null|void
     */
    public function delete()
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
            'action' => 'search',
            'account_id' => $this->request->getParam('account_id'),
            '?' => $this->request->getQueryParams(),
        ]);
    }
}
