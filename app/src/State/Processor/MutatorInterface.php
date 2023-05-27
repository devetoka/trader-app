<?php

namespace App\State\Processor;

use App\Entity\BaseEntity;

interface MutatorInterface
{
    public function mutate(BaseEntity $data, array $context): self;

    public function getData(): BaseEntity;

    public function postProcessorOperation(BaseEntity $data): void;
}