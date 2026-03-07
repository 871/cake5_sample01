<?php
declare(strict_types=1);

namespace App\Controller\SampleCode\MySqlTypeSamples;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\SampleCode\MySqlTypeSamples\Create as CtlService;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use DateTimeImmutable;

class CreateController extends AppController
{
    /**
     * @var \App\Service\Controller\SampleCode\MySqlTypeSamples\Create
     */
    private CtlService $ctlService;

    /**
     * @param \Cake\Event\EventInterface $event
     * @return ?\Cake\Http\Response
     */
    public function beforeFilter(EventInterface $event): ?Response
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

        ]);

        return $this->render('/Sample/search');
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function inputPost()
    {
        $this->set([

        
        ]);

        return $this->render('/Sample/search');
    }
}
