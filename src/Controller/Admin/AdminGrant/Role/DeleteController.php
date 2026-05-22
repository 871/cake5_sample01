<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant\Role;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\AdminGrant\Role\Delete as CtlService;
use Cake\Event\EventInterface;
use Cake\Http\Exception\MethodNotAllowedException;
use DateTimeImmutable;
use DomainException;

class DeleteController extends AppController
{
    /**
     * @var \App\Service\Controller\Admin\AdminGrant\Role\Delete
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
        try {
            $this->ctlService->delete();
            $this->Flash->success('ロール権限を削除しました。');
        } catch (DomainException) {
            $this->Flash->error('使用者がいるロールは削除できません');
        }

        return $this->redirect([
            'prefix' => 'Admin/AdminGrant/Role',
            'controller' => 'Search',
            'action' => 'index',
            'account_id' => $this->request->getParam('account_id'),
            '?' => $this->request->getQuery(),
        ]);
    }
}
