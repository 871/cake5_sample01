<?php
declare(strict_types=1);

namespace App\Controller\AdminAccounts\Accounts;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\AdminAccounts\Accounts\Detail as CtlService;
use Cake\Event\EventInterface;
use DateTimeImmutable;

class DetailController extends AppController
{
    private CtlService $ctlService;

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('admin_accounts');

        $this->ctlService = new CtlService(
            datetime: new DateTimeImmutable(),
            request: $this->request,
            authContext: AuthContextResolver::resolve($this->request),
        );
    }

    /**
     * 詳細表示
     */
    public function index()
    {
        $entity = $this->ctlService->getDomainEntity();

        $this->set([
            'entity' => $entity,
            'histories' => $this->ctlService->getHistories($entity->id()->toString()),
        ]);

        return $this->render('/AdminAccounts/Accounts/detail');
    }
}
