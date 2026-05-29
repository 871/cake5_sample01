<?php
declare(strict_types=1);

namespace App\Application\Controller\Admin;

use App\Application\Controller\Shared\ServiceInterface;
use App\Application\Controller\Shared\ServiceTrait;
use App\Security\Auth\AuthContext\Fields\Type;
use App\Security\Auth\AuthSession;
use App\Security\Input\StrictCast;
use Cake\ORM\Locator\LocatorAwareTrait;

final class Logout implements ServiceInterface
{
    use ServiceTrait;
    use LocatorAwareTrait;

    /**
     * @return self
     */
    public function logout(): self
    {
        $authSession = new AuthSession(
            request: $this->request,
            type: Type::TYPE_ADMIN,
            account_id: StrictCast::toString($this->request->getParam('account_id')),
        );

        $authSession->delete();

        return $this;
    }
}
