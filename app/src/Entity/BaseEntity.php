<?php

namespace App\Entity;

use App\Entity\Contract\Timestamp;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

class BaseEntity
{
    use Timestamp;

    #[ORM\Column]
    protected ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    protected ?DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->setTimestamps();
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }


    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }


}