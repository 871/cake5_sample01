<?php
declare(strict_types=1);

namespace App\Controller\AdminAccount;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\AdminAccount\Detail as CtlService;
use Cake\Event\EventInterface;
use DateTimeImmutable;

class DetailController extends AppController
{
    /**
     * @var \App\Service\Controller\AdminAccount\Detail
     */
    private CtlService $ctlService;

    /**
     * @param \Cake\Event\EventInterface $event
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('admin_account');

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
            'histories' => $this->ctlService->getHistories(),
        ]);

        return $this->render('/AdminAccount/detail');
    }
}
