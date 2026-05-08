<?php
declare(strict_types=1);

namespace App\Service\Controller\Admin\MailManage;

use App\Domain\Mail\Entity\Mail;
use App\Domain\Mail\ValueObject as Vo;
use App\Infrastructure\Persistence\Cake\Mail\MailsRepository;
use App\Security\Input\Cast;
use App\Security\Input\StrictCast;
use App\Service\Controller\Shared\ServiceInterface;
use App\Service\Controller\Shared\ServiceTrait;

final class Create implements ServiceInterface
{
    use ServiceTrait;
    private const DATE_TIME_FORMAT = 'Y-m-d\TH:i:s';

    /**
     * @return \App\Domain\Mail\Entity\Mail
     */
    public function create(): Mail
    {
        return (new MailsRepository())->create(new Mail(
            id: $this->resolveId(),
            related_data_key: StrictCast::toString($this->request->getData('related_data_key')),
            send_status: Vo\SendStatus::WAITING,
            send_scheduled_at: $this->resolveSendScheduledAt(),
            title: StrictCast::toString($this->request->getData('title')),
            body: StrictCast::toString($this->request->getData('body')),
            mail_to: StrictCast::toString($this->request->getData('mail_to')),
            mail_cc: Cast::toStringOrNull($this->request->getData('mail_cc')),
            mail_bcc: Cast::toStringOrNull($this->request->getData('mail_bcc')),
            mail_received_check: StrictCast::toString($this->request->getData('mail_received_check')),
            mail_return_path: StrictCast::toString($this->request->getData('mail_return_path')),
            created: $this->datetime->format(self::DATE_TIME_FORMAT),
            created_by: Cast::toStringOrNull($this->authContext->getAccountId()),
            created_ip: Cast::toStringOrNull($this->request->clientIp()),
            modified: $this->datetime->format(self::DATE_TIME_FORMAT),
            modified_by: Cast::toStringOrNull($this->authContext->getAccountId()),
            modified_ip: Cast::toStringOrNull($this->request->clientIp()),
        ));
    }

    /**
     * @return string
     */
    private function resolveId(): string
    {
        $requestedId = Cast::toStringOrNull($this->request->getData('id'));
        if ($requestedId !== null) {
            return $requestedId;
        }

        return $this->datetime->format('Uu');
    }

    /**
     * @return string
     */
    private function resolveSendScheduledAt(): string
    {
        $requested = $this->request->getData('send_scheduled_at');
        if (Cast::toStringOrNull($requested) === null) {
            return $this->datetime->format(self::DATE_TIME_FORMAT);
        }

        return StrictCast::toDateTimeString($requested);
    }
}
