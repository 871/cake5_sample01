<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant\Role;

use App\Controller\AppController;
use App\Exception\ValidateException;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\AdminGrant\Role\Create as CtlService;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use DateTimeImmutable;

class CreateController extends AppController
{
    /**
     * @var \App\Service\Controller\Admin\AdminGrant\Role\Create
     */
    private CtlService $ctlService;

    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     * @return ?\Cake\Http\Response
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('admin_main');

        $this->ctlService = new CtlService(
            datetime: new DateTimeImmutable(),
            request: $this->request,
            authContext: AuthContextResolver::resolve($this->request),
        );

        if (!$this->ctlService->existsInputProcess(ignoreActions: ['index'])) {
            return $this->redirect([
                'action' => 'index',
                'account_id' => $this->request->getParam('account_id'),
                '?' => $this->request->getQuery(),
            ]);
        }

        return null;
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $inputProcess = $this->ctlService->startInputProcess();

        return $this->redirect([
            'action' => 'input',
            'account_id' => $this->request->getParam('account_id'),
            'process_id' => $inputProcess->getId(),
            '?' => $this->request->getQuery(),
        ]);
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function input()
    {
        $this->set([
            'input' => $this->ctlService->getInputProcess(),
            'isEdit' => false,
            'grantPermissionOptions' => $this->ctlService->getGrantPermissionOptions(),
        ]);

        return $this->render('/Admin/AdminGrant/Role/input');
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function inputPost()
    {
        try {
            $this->ctlService
                ->inputProcessUpdate()
                ->inputProcessValidation();

            return $this->redirect([
                'action' => 'conf',
                'account_id' => $this->request->getParam('account_id'),
                'process_id' => $this->request->getParam('process_id'),
                '?' => $this->request->getQuery(),
            ]);
        } catch (ValidateException $ex) {
            $this->ctlService->inputProcessErrorUpdate($ex);

            return $this->redirect([
                'action' => 'input',
                'account_id' => $this->request->getParam('account_id'),
                'process_id' => $this->request->getParam('process_id'),
                '?' => $this->request->getQuery(),
            ]);
        }
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function conf()
    {
        $this->set([
            'input' => $this->ctlService->getInputProcess(),
            'isEdit' => false,
            'grantPermissionOptions' => $this->ctlService->getGrantPermissionOptions(),
        ]);

        return $this->render('/Admin/AdminGrant/Role/conf');
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function confPost()
    {
        try {
            $this->ctlService
                ->inputProcessValidation()
                ->saveInputProcess()
                ->endInputProcess();

            $this->Flash->success('ロール権限の作成が完了しました。');

            return $this->redirect([
                'prefix' => 'Admin/AdminGrant/Role',
                'controller' => 'Search',
                'action' => 'index',
                'account_id' => $this->request->getParam('account_id'),
                '?' => $this->request->getQuery(),
            ]);
        } catch (ValidateException $ex) {
            $this->ctlService->inputProcessErrorUpdate($ex);

            return $this->redirect([
                'action' => 'input',
                'account_id' => $this->request->getParam('account_id'),
                'process_id' => $this->request->getParam('process_id'),
                '?' => $this->request->getQuery(),
            ]);
        }
    }
}
