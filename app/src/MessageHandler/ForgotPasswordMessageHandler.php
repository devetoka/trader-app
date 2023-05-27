<?php

namespace App\MessageHandler;

use App\Message\ForgotPasswordMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ForgotPasswordMessageHandler
{
    public function __invoke(ForgotPasswordMessage $message)
    {
        // TODO: Implement __invoke() method.
    }

}