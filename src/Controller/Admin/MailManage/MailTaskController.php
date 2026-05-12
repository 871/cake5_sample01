<?php
declare(strict_types=1);

namespace App\Controller\Admin\MailManage;

use App\Controller\AppController;
use App\Infrastructure\Persistence\Cake\Mail\MailsRepository;
use Cake\Http\Response;

class MailTaskController extends AppController
{
    /**
     * @return \Cake\Http\Response
     */
    public function sendWaitingMails(): Response
    {
        $this->request->allowMethod(['get']);

        $count = (new MailsRepository())->sendWaitingMails();
        $this->Flash->success(__('送信待ちメールの送信処理が完了しました。（{0}件）', $count));

        return $this->redirectToSearch();
    }

    /**
     * @return \Cake\Http\Response
     */
    public function checkReceivedMails(): Response
    {
        $this->request->allowMethod(['get']);

        $count = (new MailsRepository())->checkReceivedMails();
        $this->Flash->success(__('受信確認処理が完了しました。（{0}件）', $count));

        return $this->redirectToSearch();
    }

    /**
     * @return \Cake\Http\Response
     */
    public function checkBouncedMails(): Response
    {
        $this->request->allowMethod(['get']);

        $count = (new MailsRepository())->checkBouncedMails();
        $this->Flash->success(__('バウンス確認処理が完了しました。（{0}件）', $count));

        return $this->redirectToSearch();
    }

    /**
     * @return \Cake\Http\Response
     */
    private function redirectToSearch(): Response
    {
        return $this->redirect([
            'prefix' => 'Admin/MailManage',
            'controller' => 'Search',
            'action' => 'init',
            'account_id' => $this->request->getParam('account_id'),
            '?' => $this->request->getQuery(),
        ]);
    }
}
