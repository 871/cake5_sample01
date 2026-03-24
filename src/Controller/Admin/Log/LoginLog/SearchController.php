<?php
declare(strict_types=1);

namespace App\Controller\Admin\Log\LoginLog;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\Log\LoginLog\Search as CtlService;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Log\Log;
use DateTimeImmutable;

class SearchController extends AppController
{
    /**
     * @var \App\Service\Controller\Admin\Log\LoginLog\Search
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

        $this->viewBuilder()->setLayout('admin_login_log');
    }

    /**
     * 初期アクセスでの検索パラメータ設定
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function init()
    {
        $this->redirect([
            'action' => 'index',
            'account_id' => $this->request->getParam('account_id'),
            '?' => $this->ctlService->getInitParams(),
        ]);
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        try {
            $categoryService = $this->ctlService->createCategoryService();
            
            $this->set([
                'loginActorTypeOptions' => $categoryService->getLoginActorTypeOptions(),
                'loginResultOptions' => $categoryService->getLoginResultOptions(),
                'failureReasonCodeOptions' => $categoryService->getFailureReasonCodeOptions(),
                'rows' => $this->paginate(
                    $this->ctlService->getSearchQuery(),
                    $this->ctlService->getPaginateSettings(),
                ),
            ]);
        } catch (NotFoundException $e) {
            Log::error(
                '無効なページが指定されました。'
                . '[message: ' . $e->getMessage() . ']'
                . '[Uri: ' . $this->request->getRequestTarget() . ']',
            );

            $this->Flash->warning('無効なページが指定されました。1ページ目を表示します。');

            return $this->redirect([
                'account_id' => $this->request->getParam('account_id'),
                '?' => array_merge((array)$this->request->getQuery(), [
                    'page' => 1,
                ]),
            ]);
        }

        return $this->render('/Admin/Log/LoginLog/search');
    }
}
