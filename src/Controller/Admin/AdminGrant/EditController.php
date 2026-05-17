<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant;

use App\Controller\AppController;
use App\Exception\ValidateException;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\AdminGrant\Edit as CtlService;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use DateTimeImmutable;

class EditController extends AppController
{
    /**
     * @var \App\Service\Controller\Admin\AdminGrant\Edit
     */
    private CtlService $ctlService;

    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     * @return ?\Cake\Http\Response
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        parent::beforeFilter($event);
        $this->ctlService = new CtlService(
            datetime: new DateTimeImmutable(),
            request: $this->request,
            authContext: AuthContextResolver::resolve($this->request),
        );
        $this->viewBuilder()->setLayout('admin_main');

        if (!$this->ctlService->existsInputProcess(ignoreActions: ['index'])) {
            $this->Flash->error('更新対象のデータが見つかりません。');

            return $this->redirect([
                'controller' => 'Search',
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
        /** @var \App\Service\Controller\Shared\Process\Process\InputProcess $input */
        $input = $this->ctlService->getInputProcess();

        $this->set([
            'input' => $input,
            'adminAccountId' => $input->getInput('admin_account_id'),
            'grantRoleOptions' => $this->ctlService->getGrantRoleOptions(),
            'grantPermissionOptions' => $this->ctlService->getGrantPermissionOptions(),
        ]);

        return $this->render('/Admin/AdminGrant/account_permission_input');
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
        /** @var \App\Service\Controller\Shared\Process\Process\InputProcess $input */
        $input = $this->ctlService->getInputProcess();

        $this->set([
            'input' => $input,
            'adminAccountId' => $input->getInput('admin_account_id'),
            'grantRoleOptions' => $this->ctlService->getGrantRoleOptions(),
            'grantPermissionOptions' => $this->ctlService->getGrantPermissionOptions(),
        ]);

        return $this->render('/Admin/AdminGrant/account_permission_conf');
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function confPost()
    {
        try {
            /** @var \App\Service\Controller\Shared\Process\Process\InputProcess $input */
            $input = $this->ctlService->getInputProcess();
            $adminAccountId = (string)$input->getInput('admin_account_id');

            $this->ctlService
                ->inputProcessValidation()
                ->saveInputProcess()
                ->endInputProcess();
            $this->Flash->success('管理者権限を更新しました。');

            return $this->redirect([
                'controller' => 'Detail',
                'action' => 'index',
                'account_id' => $this->request->getParam('account_id'),
                'admin_account_id' => $adminAccountId,
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
