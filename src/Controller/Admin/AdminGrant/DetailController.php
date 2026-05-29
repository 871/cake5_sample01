<?php
declare(strict_types=1);

namespace App\Controller\Admin\AdminGrant;

use App\Application\Controller\Admin\AdminGrant\Detail as CtlService;
use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Security\Input\StrictCast;
use Cake\Event\EventInterface;
use DateTimeImmutable;

class DetailController extends AppController
{
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
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $this->set([
            'adminAccountGrant' => $this->ctlService->getAdminAccountGrant(
                adminAccountId: StrictCast::toString($this->request->getParam('admin_account_id')),
            ),
            'grantPermissions' => $this->ctlService->getGrantPermissions(),
        ]);

        return $this->render('/Admin/AdminGrant/detail');
    }
}
