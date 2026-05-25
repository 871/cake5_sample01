<?php
declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AppController;
use App\Security\Auth\UserTokenService;
use App\Security\Input\Cast;
use Cake\Event\EventInterface;
use Psr\Http\Message\ResponseInterface;

class TopController extends AppController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('user_main');
    }

    public function index(): ResponseInterface
    {
        $auth = $this->request->getAttribute(UserTokenService::REQUEST_ATTRIBUTE);
        $accountName = is_array($auth)
            ? Cast::toStringOrNull($auth['account_name'] ?? null)
            : null;
        $this->set('accountName', $accountName);

        return $this->render('/User/top');
    }
}
