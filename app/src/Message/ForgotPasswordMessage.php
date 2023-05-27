<?php

namespace App\Message;

class ForgotPasswordMessage implements AsyncMessageInterface
{
    public function __construct(private string $userEmail)
    {
    }

    public function getEmail(): string
    {
        return $this->userEmail;
    }

}