<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant\RolePermission;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\AdminGrant\RolePermission\Search as CtlService;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Log\Log;
use DateTimeImmutable;

class SearchController extends AppController
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

    public function init()
    {
        return $this->redirect([
            'action' => 'index',
            'account_id' => $this->request->getParam('account_id'),
            '?' => $this->ctlService->getInitParams(),
        ]);
    }

    public function index()
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
}
