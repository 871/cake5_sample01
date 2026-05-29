<?php
declare(strict_types=1);

namespace App\Controller\Admin\MailManage;

use App\Controller\AppController;
use App\Exception\ValidateException;
use App\Security\Auth\AuthContextResolver;
use App\Application\Controller\Admin\MailManage\Search as CtlService;
use Cake\Event\EventInterface;
use DateTimeImmutable;

class SearchController extends AppController
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
     * 初期アクセスでの検索パラメータ設定
     */
    public function init(): void
    {
        $this->redirect([
            'action' => 'index',
            'account_id' => $this->request->getParam('account_id'),
            '?' => $this->ctlService->getInitParams(),
        ]);
    }

    /**
     * 大規模データ検索・一覧表示
     *
     * @return \Cake\Http\Response|null|void Renders view
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
                'errorMessages' => [],
                'errorFields' => [],
            ]);
        } catch (ValidateException $ex) {
            $this->set([
                'rows' => [],
                'isPrevExists' => false,
                'isNextExists' => false,
                'errorMessages' => $ex->getErrorMessages(),
                'errorFields' => $ex->getErrorFields(),
            ]);
        }

        return $this->render('/Admin/MailManage/search');
    }
}
