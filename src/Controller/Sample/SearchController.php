<?php
declare(strict_types=1);

namespace App\Controller\Sample;

use App\Controller\AppController;
use App\Service\Controller\Sample as CategoryService;
use App\Service\Controller\Sample\Search as CtlService;

/**
 *
 */
class SearchController extends AppController
{
    /**
     * @var CategoryService
     */
    private CategoryService $categoryService;

    /**
     * @var CtlService
     */
    private CtlService $ctlService;


    public function initialize(): void
    {
        parent::initialize();

        $this->categoryService = CategoryService::getInstance()
            ->setDatetime(new \DateTimeImmutable())
            ->setAuthContext($this->authContext)
            ->setRequest($this->request);

        $this->ctlService = $this->categoryService
            ->createService(CtlService::class);
    }

    /**
     * 初期アクセスでの検索パラメータ設定
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function init()
    {
        $this->request->redirect([
            'action' => 'index',
            '?' => $this->ctlService->getInitParams(),
        ]);
    }

    /**
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->ctlService->getSearchQuery();
        $this->set([
            'rows' => $this->paginate($query),
        ]);
    }
}
