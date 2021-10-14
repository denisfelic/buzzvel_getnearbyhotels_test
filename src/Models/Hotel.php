<?php

namespace DenisFelic\BuzzVelHotelsTest\Models;


class Hotel
{
    private string $name;
    private float $latitude;
    private float $longitude;
    private  $price;
    private float $distance;

    public function __construct(string $name, float $latitude, float $longitude, $price)
    {
        $this->name = $name;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->price = $price;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setDistance(float $distance)
    {
        $this->distance = floatval($distance);
        $this->formatedDIstance = $this->getFormatedDistance();
    }

    public function getDistance()
    {
        return $this->distance;
    }

    public function getFormatedDistance()
    {
        return number_format($this->distance, 2, '.', '.');
    }
}
