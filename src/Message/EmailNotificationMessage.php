<?php

namespace App\Message;

class EmailNotificationMessage
{
    public function __construct(
        private string $email,
        private string $subject,
        private string $content
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
