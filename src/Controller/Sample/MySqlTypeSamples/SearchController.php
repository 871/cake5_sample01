<?php
declare(strict_types=1);

namespace App\Controller\Sample\MySqlTypeSamples;

use \App\Controller\AppController;
use \App\Service\Controller\Sample\MySqlTypeSamples\Search as CtlService;
use \App\Security\Auth\AuthContextResolver;
use \Cake\Http\Exception\NotFoundException;
use \Cake\Log\Log;

/**
 *
 */
class SearchController extends AppController
{
    /**
     * @var CtlService
     */
    private CtlService $ctlService;


    public function initialize(): void
    {
        parent::initialize();

        $this->ctlService = new CtlService(
            datetime: new \DateTimeImmutable(),
            request: $this->request,
            authContext: AuthContextResolver::resolve($this->request)
        );

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
            $this->set([
                'rows' => $this->paginate(
                    $this->ctlService->getSearchQuery(),
                    $this->ctlService->getPaginateSettings()
                ),
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
