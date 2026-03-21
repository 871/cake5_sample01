<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Model\Table\Admin\AdminAccountsTable;
use App\Security\Auth\AuthContext\Fields\AccountStatusMasterCode;
use App\Security\Auth\AuthContext\Fields\Type;
use App\Security\Auth\AuthSession;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\ORM\Locator\LocatorAwareTrait;
use DateTimeImmutable;

class LoginController extends AppController
{
    use LocatorAwareTrait;

    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('admin_login');
    }

    /**
     * ログイン画面表示
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index(): void
    {
    }

    /**
     * ログイン処理
     *
     * @return \Cake\Http\Response
     */
    public function indexPost(): Response
    {
        /** @var string $email */
        $email = $this->request->getData('email', '');
        /** @var string $password */
        $password = $this->request->getData('password', '');

        /** @var \App\Model\Table\Admin\AdminAccountsTable $table */
        $table = $this->fetchTable(AdminAccountsTable::class);

        /** @var \App\Model\Entity\Admin\AdminAccount|null $account */
        $account = $table->find()
            ->contain(['AccountStatusMasters'])
            ->where(['AdminAccounts.email' => $email])
            ->first();

        if ($account === null || !password_verify($password, $account->password)) {
            $this->Flash->error('メールアドレスまたはパスワードが違います。');

            return $this->redirect(['action' => 'index']);
        }

        if ($account->account_status_master->code !== AccountStatusMasterCode::ACTIVE) {
            $this->Flash->error('このアカウントはログインできません。');

            return $this->redirect(['action' => 'index']);
        }

        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $this->request->getSession()->renew();

        (new AuthSession(
            request: $this->request,
            type: Type::TYPE_ADMIN,
            account_id: (string)$account->id,
        ))->write([
            'account_id' => (string)$account->id,
            'account_email' => $account->email,
            'account_name' => $account->name,
            'account_status_master_id' => (string)$account->account_status_master_id,
            'account_status_master_name' => $account->account_status_master->name,
            'account_status_master_code' => $account->account_status_master->code,
            'is_email_verified' => $account->is_email_verified ? '1' : '0',
            'password_changed_at' => $account->password_changed_at->format('Y-m-d H:i:s'),
            'password_expires_at' => $account->password_expires_at->format('Y-m-d H:i:s'),
            'created' => $account->created->format('Y-m-d H:i:s'),
            'modified' => $account->modified->format('Y-m-d H:i:s'),
            'logined' => $now,
        ]);

        return $this->redirect("/v1/ad/{$account->id}/");
    }
}
