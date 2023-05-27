<?php

namespace App\State\Processor\User;

use ApiPlatform\State\ProcessorInterface;
use App\Entity\BaseEntity;
use App\Entity\User;
use App\State\Processor\MutatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DeleteOperation implements MutatorInterface
{
    private ?BaseEntity $mutatedData = null;

    public function mutate(mixed $data, array $context = []) : self
    {
        //perform mutation  here
    }



    public function getData(): BaseEntity
    {
        return $this->mutatedData;
    }

    public function postProcessorOperation(BaseEntity $data): void
    {
        // TODO: Implement postProcessorOperation() method.
    }
}