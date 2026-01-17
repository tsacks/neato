<?php

namespace App\Entity;

use App\Enum\WeatherTimeEnum;
use App\Repository\WMOCodeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WMOCodeRepository::class)]
class WMOCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $code = null;

    #[ORM\Column(enumType: WeatherTimeEnum::class)]
    private ?WeatherTimeEnum $time = null;

    #[ORM\Column(length: 30)]
    private ?string $description = null;

    #[ORM\Column(length: 30)]
    private ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getTime(): ?WeatherTimeEnum
    {
        return $this->time;
    }

    public function setTime(WeatherTimeEnum $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
