<?php

namespace App\MessageHandler;

use App\Message\EmailNotificationMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class EmailNotificationMessageHandler
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function __invoke(EmailNotificationMessage $message)
    {
        $email = (new Email())
            ->from('noreply@librashelf.com')
            ->to($message->getEmail())
            ->subject($message->getSubject())
            ->text($message->getContent());

        $this->mailer->send($email);
    }
}
