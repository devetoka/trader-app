<?php

namespace App\Message;

class NewUserMessage implements AsyncMessageInterface
{
    public function __construct(public int $userId)
    {
    }

}