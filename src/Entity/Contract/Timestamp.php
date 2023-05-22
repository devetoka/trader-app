<?php

namespace App\Entity\Contract;

use DateTimeImmutable;

trait Timestamp
{
    public function setTimestamps()
    {
        if (method_exists($this, 'setCreatedAt')) {
            $this->setCreatedAt(new DateTimeImmutable());
        }

        // check if doctrine is in an update state
        if (method_exists($this, 'setUpdatedAt')) {
            $this->setUpdatedAt(new DateTimeImmutable());
        }
    }

}