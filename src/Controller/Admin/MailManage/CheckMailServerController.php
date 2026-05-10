<?php
declare(strict_types=1);

namespace App\Controller\Admin\MailManage;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Mailer\Mailer;
use Cake\Core\Configure;
use Webklex\PHPIMAP\ClientManager;


class CheckMailServerController extends AppController
{
    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
     * @return ?\Cake\Http\Response
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        parent::beforeFilter($event);

        return null;
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->autoRender = false;

        return $this->response
            ->withType('text/html')
            ->withStringBody(<<<HTML
                <html>
                    <head><title>Check Mail Server</title></head>
                    <body>
                        <h1>Check Mail Server</h1>
                        <ul>
                            <li>SMTP送信サーバ接続[<span id="smtp-status">確認待ち</span>]</li>
                            <li>IMAP受信確認用サーバ接続[<span id="imap-status">確認待ち</span>]</li>
                            <li>IMAPエラーメール受信サーバ接続[<span id="imap-error-status">確認待ち</span>]</li>
                        </ul>
                        <script>
                            (async () => {
                                const response = await fetch(location.href + '/sent_smtp');
                                document.getElementById('smtp-status').innerHTML = await response.text();
                            })();

                            (async () => {
                                const response = await fetch(location.href + '/received_check_imap');
                                document.getElementById('imap-status').innerHTML = await response.text();
                            })();

                            (async () => {
                                const response = await fetch(location.href + '/return_path_imap');
                                document.getElementById('imap-error-status').innerHTML = await response.text();
                            })();
                        </script>
                    </body>
                </html>
            
            HTML);
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function sentSmtp()
    {
        $this->autoRender = false;

        try {
            $mailer = new Mailer('default');
            $mailer
                ->setTo('test@example.com')
                ->setSubject('SMTP Test')
                ->deliver('connection test');

            return $this->response
                ->withType('text/html')
                ->withStringBody('<span style="background-color: green; color:#fff;">SMTP OK</span>');
        } catch (\Throwable $e) {
            return $this->response
                ->withStatus(500)
                ->withType('text/html')
                ->withStringBody('<span style="background-color: red; color:#fff;">' . $e->getMessage() . '</span>');
        }
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function receivedCheckImap()
    {
        $this->autoRender = false;

        try {
            $client = (new ClientManager())
                ->make(Configure::read('PHPIMAP.received_check'));
            $client->connect();
            $client->disconnect();

            return $this->response
                ->withType('text/html')
                ->withStringBody('<span style="background-color: green; color:#fff;">IMAP OK</span>');
        } catch (\Throwable $e) {
            return $this->response
                ->withStatus(500)
                ->withType('text/html')
                ->withStringBody('<span style="background-color: red; color:#fff;">' . $e->getMessage() . '</span>');
        }
    }

    /**
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function returnPathImap()
    {
        $this->autoRender = false;

        try {
            $client = (new ClientManager())
                ->make(Configure::read('PHPIMAP.return_path'));
            $client->connect();
            $client->disconnect();

            return $this->response
                ->withType('text/html')
                ->withStringBody('<span style="background-color: green; color:#fff;">IMAP OK</span>');
        } catch (\Throwable $e) {
            return $this->response
                ->withStatus(500)
                ->withType('text/html')
                ->withStringBody('<span style="background-color: red; color:#fff;">' . $e->getMessage() . '</span>');
        }
    }
}
