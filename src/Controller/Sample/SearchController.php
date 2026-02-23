<?php
declare(strict_types=1);

namespace App\Controller\Sample;

use \App\Controller\AppController;
use \App\Service\Controller\Sample as CategoryService;
use \App\Service\Controller\Sample\Search as CtlService;
use \Cake\Http\Exception\NotFoundException;
use Cake\Log\Log;

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
            ->setDatetime($this->datetime)
            ->setAuthContext($this->authContext)
            ->setRequest($this->request);

        $this->ctlService = $this->categoryService
            ->createService(CtlService::class);

        $this->viewBuilder()
            ->setLayout('sample');
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
            '?' => $this->ctlService->getInitParams(),
        ]);
    }

    /**
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        try {
            $query = $this->ctlService->getSearchQuery();
            $settings = $this->ctlService->getSearchSettings();
            $this->set([
                'rows' => $this->paginate($query, $settings),
            ]);
        } catch (NotFoundException $e) {

            Log::error(
                '無効なページが指定されました。'
                . '[message: ' . $e->getMessage() . ']'
                . '[Uri: ' . $this->request->getRequestTarget() . ']'
            );

            $this->Flash->warning('無効なページが指定されました。1ページ目を表示します。');
            return $this->redirect([
                '?' => array_merge($this->request->getQuery(), [
                    'page' => 1,
                ]),
            ]);
        }

        return $this->render('/Sample/search');
    }
}
