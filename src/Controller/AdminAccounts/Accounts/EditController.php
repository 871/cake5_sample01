<?php
declare(strict_types=1);

namespace App\Controller\AdminAccounts\Accounts;

use App\Controller\AppController;
use App\Exception\ValidateException;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\AdminAccounts\Accounts\Edit as CtlService;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use DateTimeImmutable;

class EditController extends AppController
{
    private CtlService $ctlService;

    public function beforeFilter(EventInterface $event): ?Response
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('admin_accounts');

        $this->ctlService = new CtlService(
            datetime: new DateTimeImmutable(),
            request: $this->request,
            authContext: AuthContextResolver::resolve($this->request),
        );

        if (!$this->ctlService->existsInputProcess(ignoreActions: ['index'])) {
            $this->Flash->error(__('更新対象のデータが見つかりません。'));

            return $this->redirect([
                'controller' => 'Search',
                'action' => 'index',
                '?' => $this->request->getQuery(),
            ]);
        }

        return null;
    }

    /**
     * 更新開始
     */
    public function index()
    {
        $inputProcess = $this->ctlService->startInputProcess();

        return $this->redirect([
            'action' => 'input',
            'process_id' => $inputProcess->getId(),
            '?' => $this->request->getQuery(),
        ]);
    }

    /**
     * 入力画面表示
     */
    public function input()
    {
        $this->set([
            'input' => $this->ctlService->getInputProcess(),
            'roles' => $this->ctlService->getRoles(),
        ]);

        return $this->render('/AdminAccounts/Accounts/input');
    }

    /**
     * 入力画面POST処理
     */
    public function inputPost()
    {
        try {
            $this->ctlService
                ->inputProcessUpdate()
                ->inputProcessValidation();

            return $this->redirect([
                'action' => 'conf',
                'process_id' => $this->request->getParam('process_id'),
                '?' => $this->request->getQuery(),
            ]);
        } catch (ValidateException $ex) {
            $this->ctlService->inputProcessErrorUpdate($ex);

            return $this->redirect([
                'action' => 'input',
                'process_id' => $this->request->getParam('process_id'),
                '?' => $this->request->getQuery(),
            ]);
        }
    }

    /**
     * 確認画面表示
     */
    public function conf()
    {
        $this->set([
            'input' => $this->ctlService->getInputProcess(),
            'roles' => $this->ctlService->getRoles(),
        ]);

        return $this->render('/AdminAccounts/Accounts/conf');
    }

    /**
     * 確認画面POST - データ保存
     */
    public function confPost()
    {
        try {
            $this->ctlService
                ->inputProcessValidation()
                ->saveInputProcess()
                ->endInputProcess();

            $this->Flash->success(__('管理者アカウントの更新が完了しました。'));

            return $this->redirect([
                'controller' => 'Search',
                'action' => 'index',
                '?' => $this->request->getQuery(),
            ]);
        } catch (ValidateException $ex) {
            $this->ctlService->inputProcessErrorUpdate($ex);

            return $this->redirect([
                'action' => 'input',
                'process_id' => $this->request->getParam('process_id'),
                '?' => $this->request->getQuery(),
            ]);
        }
    }
}
