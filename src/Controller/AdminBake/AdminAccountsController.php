<?php
declare(strict_types=1);

namespace App\Controller\AdminBake;

use App\Controller\AppController;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Security\Input\StrictCast;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Http\Response;
use Cake\I18n\DateTime;

/**
 * AdminAccounts Controller
 *
 * @property \App\Model\Table\Admin\AdminAccountsTable $AdminAccounts
 */
class AdminAccountsController extends AppController
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->AdminAccounts = $this->fetchTable(AdminAccountsTable::class);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->AdminAccounts->find()
            ->contain(['AccountStatusMasters']);
        $adminAccounts = $this->paginate($query);

        $this->set(compact('adminAccounts'));
    }

    /**
     * View method
     *
     * @param string|null $id Admin Account id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $adminAccount = $this->AdminAccounts->get($id, contain: ['AccountStatusMasters', 'AdminAccountHistories']);
        $this->set(compact('adminAccount'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $adminAccount = $this->AdminAccounts->newEmptyEntity();
        if ($this->request->is('post')) {
            $adminAccount = $this->AdminAccounts->patchEntity(
                $adminAccount,
                array_merge((array)$this->request->getData(), [
                    'password' => (new DefaultPasswordHasher())->hash(
                        StrictCast::toString($this->request->getData('password')),
                    ),
                ]),
            );
            $adminAccount->created = new DateTime();
            $adminAccount->modified = new DateTime();

            if ($this->AdminAccounts->save($adminAccount)) {
                $this->Flash->success(__('The admin account has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The admin account could not be saved. Please, try again.'));
        }
        $accountStatusMasters = $this->AdminAccounts->AccountStatusMasters->find('list', limit: 200)->all();
        $this->set(compact('adminAccount', 'accountStatusMasters'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Admin Account id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $adminAccount = $this->AdminAccounts->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $adminAccount = $this->AdminAccounts->patchEntity($adminAccount, (function () use ($adminAccount): array {

                return $adminAccount->password === $this->request->getData('password')
                    ? (array)$this->request->getData()
                    : array_merge((array)$this->request->getData(), [
                        'password' => (new DefaultPasswordHasher())->hash(
                            StrictCast::toString($this->request->getData('password')),
                        ),
                    ]);
            })());
            $adminAccount->modified = new DateTime();

            if ($this->AdminAccounts->save($adminAccount)) {
                $this->Flash->success(__('The admin account has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The admin account could not be saved. Please, try again.'));
        }
        $accountStatusMasters = $this->AdminAccounts->AccountStatusMasters->find('list', limit: 200)->all();
        $this->set(compact('adminAccount', 'accountStatusMasters'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Admin Account id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null): ?Response
    {
        $this->request->allowMethod(['post', 'delete']);
        $adminAccount = $this->AdminAccounts->get($id);
        if ($this->AdminAccounts->delete($adminAccount)) {
            $this->Flash->success(__('The admin account has been deleted.'));
        } else {
            $this->Flash->error(__('The admin account could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
