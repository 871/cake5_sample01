<?php
declare(strict_types=1);

namespace App\Controller\AdminAccounts\Accounts;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\AdminAccounts\Accounts\Search as CtlService;
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

        $this->viewBuilder()->setLayout('admin_accounts');
    }

    /**
     * 初期アクセスでの検索パラメータ設定
     */
    public function init()
    {
        $this->redirect([
            'action' => 'index',
            '?' => $this->ctlService->getInitParams(),
        ]);
    }

    /**
     * 検索結果表示
     */
    public function index()
    {
        try {
            $this->set([
                'rows' => $this->paginate(
                    $this->ctlService->getSearchQuery(),
                    $this->ctlService->getPaginateSettings(),
                ),
                'roles' => $this->ctlService->getRoles(),
            ]);
        } catch (NotFoundException $e) {
            Log::error(
                '無効なページが指定されました。'
                . '[message: ' . $e->getMessage() . ']'
                . '[Uri: ' . $this->request->getRequestTarget() . ']',
            );

            $this->Flash->warning('無効なページが指定されました。1ページ目を表示します。');

            return $this->redirect([
                '?' => array_merge((array)$this->request->getQuery(), [
                    'page' => 1,
                ]),
            ]);
        }

        return $this->render('/AdminAccounts/Accounts/search');
    }
}
