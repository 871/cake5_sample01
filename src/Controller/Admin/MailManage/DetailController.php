<?php
declare(strict_types=1);

namespace App\Controller\Admin\MailManage;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\Admin\MailManage\Detail as CtlService;
use Cake\Event\EventInterface;
use DateTimeImmutable;

class DetailController extends AppController
{
    /**
     * @var \App\Service\Controller\Admin\MailManage\Detail
     */
    private CtlService $ctlService;

    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('admin_mail_manage');

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
            'entity' => $this->ctlService->getEntity(),
        ]);

        return $this->render('/Admin/MailManage/detail');
    }
}
