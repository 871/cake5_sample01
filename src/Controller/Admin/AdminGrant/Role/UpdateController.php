<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant\Role;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\AdminGrant\Role\Update as CtlService;
use Cake\Event\EventInterface;
use DateTimeImmutable;
use DomainException;

class UpdateController extends AppController
{
    /**
     * @var \App\Service\Controller\Admin\AdminGrant\Role\Update
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

        $this->viewBuilder()->setLayout('admin_main');
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->set([
            'entity' => $this->ctlService->getDomainEntity(),
        ]);

        return $this->render('/Admin/AdminGrant/Role/update');
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function indexPost()
    {
        try {
            $this->ctlService->update();
        } catch (DomainException $e) {
            $this->Flash->error('入力内容に誤りがあります。');

            return $this->redirect([
                'action' => 'index',
                'account_id' => $this->request->getParam('account_id'),
                'grant_role_id' => $this->request->getParam('grant_role_id'),
                '?' => $this->request->getQuery(),
            ]);
        }

        $this->Flash->success('ロール権限を更新しました。');

        return $this->redirect([
            'prefix' => 'Admin/AdminGrant/Role',
            'controller' => 'Detail',
            'action' => 'index',
            'account_id' => $this->request->getParam('account_id'),
            'grant_role_id' => $this->request->getParam('grant_role_id'),
            '?' => $this->request->getQuery(),
        ]);
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function input()
    {
        return $this->index();
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function inputPost()
    {
        return $this->indexPost();
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function conf()
    {
        return $this->index();
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function confPost()
    {
        return $this->indexPost();
    }
}
