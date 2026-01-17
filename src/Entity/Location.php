<?php

namespace App\Entity;

class Location
{
    protected string $city;
    protected ?string $country;

    public function __construct() {
        $this->city = '';
        $this->country = null;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }
}