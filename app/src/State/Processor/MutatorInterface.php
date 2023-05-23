<?php

namespace App\State\Processor;

use App\Entity\BaseEntity;

interface MutatorInterface
{
    public function mutate(BaseEntity $data): self;

    public function getData(): BaseEntity;

    public function postProcessorOperation(): void;
}