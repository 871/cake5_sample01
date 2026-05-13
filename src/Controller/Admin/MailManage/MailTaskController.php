<?php
declare(strict_types=1);

namespace App\Controller\Admin\MailManage;

use App\Controller\AppController;
use App\Infrastructure\Persistence\Cake\Mail\MailsRepository;
use Cake\Http\Response;
use DateTimeImmutable;

class MailTaskController extends AppController
{
    /**
     * @return \Cake\Http\Response
     */
    public function sendWaitingMails(): Response
    {
        // Memo: バッチ呼び出しの機能をコントローラから呼び出すための管理者向け機能
        // 排他制御は行っていないため、複数回呼び出すと重複して処理される可能性がある点に注意
        $count = (new MailsRepository())->sendWaitingMails(new DateTimeImmutable());
        $this->Flash->success(__('送信待ちメールの送信処理が完了しました。（{0}件）', $count));

        return $this->redirectToSearch();
    }

    /**
     * @return \Cake\Http\Response
     */
    public function checkReceivedMails(): Response
    {
        // Memo: バッチ呼び出しの機能をコントローラから呼び出すための管理者向け機能
        // 排他制御は行っていないため、複数回呼び出すと重複して処理される可能性がある点に注意
        $count = (new MailsRepository())->checkReceivedMails(new DateTimeImmutable());
        $this->Flash->success(__('受信確認処理が完了しました。（{0}件）', $count));

        return $this->redirectToSearch();
    }

    /**
     * @return \Cake\Http\Response
     */
    public function checkBouncedMails(): Response
    {
        // Memo: バッチ呼び出しの機能をコントローラから呼び出すための管理者向け機能
        // 排他制御は行っていないため、複数回呼び出すと重複して処理される可能性がある点に注意
        $count = (new MailsRepository())->checkBouncedMails(new DateTimeImmutable());
        $this->Flash->success(__('バウンス確認処理が完了しました。（{0}件）', $count));

        return $this->redirectToSearch();
    }

    /**
     * @return \Cake\Http\Response
     */
    private function redirectToSearch(): Response
    {
        /** @var \Cake\Http\Response $response */
        $response = $this->redirect([
            'prefix' => 'Admin/MailManage',
            'controller' => 'Search',
            'action' => 'index',
            'account_id' => $this->request->getParam('account_id'),
            '?' => $this->request->getQuery(),
        ]);

        return $response;
    }
}
