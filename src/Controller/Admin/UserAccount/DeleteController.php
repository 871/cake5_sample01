<?php
declare(strict_types=1);

namespace App\Controller\Admin\UserAccount;

use App\Application\Controller\Admin\UserAccount\Delete as CtlService;
use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use Cake\Event\EventInterface;
use Cake\Http\Exception\MethodNotAllowedException;
use DateTimeImmutable;

class DeleteController extends AppController
{
    /**
     * @var \App\Application\Controller\Admin\UserAccount\Delete
     */
    private CtlService $ctlService;

    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     */
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
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        throw new MethodNotAllowedException();
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function indexPost()
    {
        $this->ctlService->delete();

        $this->Flash->success(__('ユーザアカウントの削除が完了しました。'));

        return $this->redirect([
            'prefix' => 'Admin/UserAccount',
            'controller' => 'Search',
            'action' => 'index',
            'account_id' => $this->request->getParam('account_id'),
            '?' => $this->request->getQuery(),
        ]);
    }
}
