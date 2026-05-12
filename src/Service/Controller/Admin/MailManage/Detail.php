<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\MailManage;

use App\Domain\Mail\Entity\Mail as DomainEntity;
use App\Domain\Mail\ValueObject\Id;
use App\Infrastructure\Persistence\Cake\Mail\MailsRepository;
use App\Security\Input\StrictCast;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Detail implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function getEntity(): DomainEntity
    {
        return (new MailsRepository())->read(
            id: new Id(
                StrictCast::toString($this->request->getParam('mail_id')),
            ),
        );
    }
}
