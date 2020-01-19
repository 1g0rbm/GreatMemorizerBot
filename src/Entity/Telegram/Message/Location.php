<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Telegram\Message;

final class Location
{
    private float $latitude;

    private float $longitude;

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }
}
