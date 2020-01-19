<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\TimeZone;

final class TimeZone
{
    private string $status;

    private ?string $message = null;

    private string $countryCode;

    private ?string $countryName = null;

    private string $zoneName;

    public static function createFromResponse(array $response): self
    {
        $timezone = new self();
        $timezone->status      = $response['status'];
        $timezone->message     = $response['message'];
        $timezone->countryCode = $response['countryCode'];
        $timezone->countryName = $response['countryName'] ?? null;
        $timezone->zoneName    = $response['zoneName'];

        return $timezone;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    public function setCountryName(?string $countryName): void
    {
        $this->countryName = $countryName;
    }

    public function getZoneName(): string
    {
        return $this->zoneName;
    }

    public function setZoneName(string $zoneName): void
    {
        $this->zoneName = $zoneName;
    }
}