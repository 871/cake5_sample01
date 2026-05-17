<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\AdminGrant\AccountPermission as CtlService;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Log\Log;
use DateTimeImmutable;
use Throwable;

class AccountPermissionController extends AppController
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
                'grantRoleOptions' => $this->ctlService->getGrantRoleOptions(),
                'grantPermissionOptions' => $this->ctlService->getGrantPermissionOptions(),
                'accountStatusOptions' => $this->ctlService->getAccountStatusOptions(),
            ]);
        } catch (NotFoundException $e) {
            Log::error('無効なページが指定されました。[message: ' . $e->getMessage() . ']');
            $this->Flash->warning('無効なページが指定されました。1ページ目を表示します。');

            return $this->redirect([
                'account_id' => $this->request->getParam('account_id'),
                '?' => array_merge((array)$this->request->getQuery(), ['page' => 1]),
            ]);
        }

        return $this->render('/Admin/AdminGrant/account_permission');
    }

    /**
     * @return \Cake\Http\Response|null|void
     */
    public function detail()
    {
        $adminAccountId = (string)$this->request->getParam('admin_account_id');

        $this->set([
            'adminAccountId' => $adminAccountId,
            'permissions' => $this->ctlService->getAccountPermissions($adminAccountId),
        ]);

        return $this->render('/Admin/AdminGrant/account_permission_detail');
    }

    /**
     * @return \Cake\Http\Response|null|void
     */
    public function edit()
    {
        $adminAccountId = (string)$this->request->getParam('admin_account_id');

        if ($this->request->is(['post', 'put'])) {
            try {
                $this->ctlService->save($adminAccountId);
                $this->Flash->success('管理者権限を更新しました。');

                return $this->redirect([
                    'action' => 'detail',
                    'account_id' => $this->request->getParam('account_id'),
                    'admin_account_id' => $adminAccountId,
                    '?' => $this->request->getQueryParams(),
                ]);
            } catch (Throwable $e) {
                Log::error($e->getMessage());
                $this->Flash->error('管理者権限の更新に失敗しました。');
            }
        }

        $this->set([
            'adminAccountId' => $adminAccountId,
            'grantRoleOptions' => $this->ctlService->getGrantRoleOptions(),
            'grantPermissionOptions' => $this->ctlService->getGrantPermissionOptions(),
            'grantedRoleIds' => $this->ctlService->getGrantedRoleIds($adminAccountId),
            'grantedPermissionIds' => $this->ctlService->getGrantedPermissionIds($adminAccountId),
        ]);

        return $this->render('/Admin/AdminGrant/account_permission_edit');
    }
}
