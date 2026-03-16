<?php
declare(strict_types=1);

namespace App\Controller\AdminAccounts\Accounts;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\AdminAccounts\Accounts\Delete as CtlService;
use Cake\Event\EventInterface;
use Cake\Http\Exception\MethodNotAllowedException;
use DateTimeImmutable;

class DeleteController extends AppController
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
    }

    /**
     * GET は許可しない
     */
    public function index()
    {
        throw new MethodNotAllowedException();
    }

    /**
     * 削除POST処理
     */
    public function indexPost()
    {
        $this->ctlService->delete();

        $this->Flash->success(__('管理者アカウントの削除が完了しました。'));

        return $this->redirect([
            'controller' => 'Search',
            'action' => 'index',
            '?' => $this->request->getQuery(),
        ]);
    }
}
