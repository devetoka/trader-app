<?php

namespace App\State\Processor\User;

use ApiPlatform\State\ProcessorInterface;
use App\Entity\BaseEntity;
use App\Entity\User;
use App\State\Processor\MutatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PostOperation implements MutatorInterface
{
    private ?BaseEntity $mutatedData = null;

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ){
    }


    public function mutate(mixed $data) : self
    {
        if ($data instanceof User && $data->getPlainPassword()) {
            $data->setPassword($this->userPasswordHasher->hashPassword($data, $data->getPlainPassword()));
        }
        $this->mutatedData = $data;

        return $this;
    }

    public function postProcessorOperation(): void
    {
        // Create a Message Notification
    }

    public function getData(): BaseEntity
    {
        return $this->mutatedData;
    }
}