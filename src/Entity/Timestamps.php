<?php

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait Timestamps
{
    #[ORM\Column(name:'created_at',type:'datetime_immutable',options:['default' => 'CURRENT_TIMESTAMP'])]
    private $createdAt;

    #[ORM\Column(name:'updated_at',type:'datetime',nullable:true)]
    private $updatedAt;

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new DateTimeImmutable();
        $this->setUpdatedAt();
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }
    
    #[ORM\PreUpdate]
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new DateTime();
        return $this;
    }
}