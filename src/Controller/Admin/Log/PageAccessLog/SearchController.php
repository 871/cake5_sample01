<?php
declare(strict_types=1);

namespace App\Controller\Admin\Log\PageAccessLog;

use App\Controller\AppController;
use App\Exception\ValidateException;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\Log\PageAccessLog\Search as CtlService;
use Cake\Event\EventInterface;
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

        $this->viewBuilder()->setLayout('admin_page_access_log');
    }

    /**
     * 初期アクセスでの検索パラメータ設定
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
     * 大規模データ検索・一覧表示
     */
    public function index()
    {
        try {
            $this->ctlService
                ->validate()
                ->search();

            $this->set([
                'rows' => $this->ctlService->getRows(),
                'isPrevExists' => $this->ctlService->isPrevExists(),
                'isNextExists' => $this->ctlService->isNextExists(),
                'errorMeesasges' => [],
                'errorFields' => []
            ]);
        } catch (ValidateException $ex) {

            $this->set([
                'rows' => [],
                'isPrevExists' => false,
                'isNextExists' => false,
                'errorMeesasges' => $ex->getErrorMeesasges(),
                'errorFields' => $ex->getErrorFields(),
            ]);
        }

        return $this->render('/Admin/Log/PageAccessLog/search');
    }
}
