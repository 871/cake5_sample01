<?php
declare(strict_types=1);

namespace App\Controller\SampleCode\MySqlTypeSamples;

use App\Controller\AppController;
use App\Security\Auth\AuthContextResolver;
use App\Service\Controller\SampleCode\MySqlTypeSamples\Delete as CtlService;
use Cake\Event\EventInterface;
use Cake\Http\Exception\MethodNotAllowedException;
use DateTimeImmutable;

class DeleteController extends AppController
{
    /**
     * @var \App\Service\Controller\SampleCode\MySqlTypeSamples\Delete
     */
    private CtlService $ctlService;

    /**
     * @param \Cake\Event\EventInterface $event
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

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
        throw new MethodNotAllowedException();
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function indexPost()
    {
        $this->ctlService->delete();

        $this->Flash->success(__('MySqlTypeSampleの削除が完了しました。'));

        return $this->redirect([
            'controller' => 'Search',
            'action' => 'index',
            '?' => $this->request->getQuery(),
        ]);
    }
}
