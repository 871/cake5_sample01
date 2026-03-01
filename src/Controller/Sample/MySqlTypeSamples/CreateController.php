<?php
declare(strict_types=1);

namespace App\Controller\Sample\MySqlTypeSamples;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Sample\MySqlTypeSamples\Create as CtlService;
use Cake\Event\EventInterface;
use DateTimeImmutable;

class CreateController extends AppController
{
    /**
     * @var \App\Service\Controller\Sample\MySqlTypeSamples\Create
     */
    private CtlService $ctlService;

    /**
     * @param EventInterface $event
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('sample');

        $this->ctlService = new CtlService(
            datetime: new DateTimeImmutable(),
            request: $this->request,
            authContext: AuthContextResolver::resolve($this->request),
        );

        if (
            !$this->ctlService->existsInputProcess(
                ignoreActions: ['index', 'copy'],
            )
        ) {
            return $this->redirect([
                'action' => 'index',
                '?' => $this->request->getQuery(),
            ]);
        }
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
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
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function input()
    {
        return $this->render('/Sample/search');
    }
}
