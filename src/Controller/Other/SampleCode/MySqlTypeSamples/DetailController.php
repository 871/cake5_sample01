<?php
declare(strict_types=1);

namespace App\Controller\Other\SampleCode\MySqlTypeSamples;

use App\Application\Controller\Other\SampleCode\MySqlTypeSamples\Detail as CtlService;
use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use Cake\Event\EventInterface;
use DateTimeImmutable;

class DetailController extends AppController
{
    /**
     * @var \App\Application\Controller\Other\SampleCode\MySqlTypeSamples\Detail
     */
    private CtlService $ctlService;

    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('sample');

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
        $this->set([
            'entity' => $this->ctlService->getDomainEntity(),
        ]);

        return $this->render('/Other/SampleCode/MySqlTypeSamples/detail');
    }
}
