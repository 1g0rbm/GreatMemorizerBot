<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Billing;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ig0rbm\Memo\Entity\Account;
use Symfony\Component\Validator\Constraints as Assert;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(name="licenses")
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\Billing\LicenseRepository")
 */
class License
{
    public const DEFAULT_TERM = 6;

    public const PROVIDER_DEFAULT = 'memo';
    public const PROVIDER_PATREON = 'patreon';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @Assert\NotBlank
     * @Assert\Type("integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     *
     * @Assert\NotBlank
     * @Assert\DateTime()
     */
    private DateTimeImmutable $dateStart;

    /**
     * @ORM\Column(type="datetime_immutable")
     *
     * @Assert\NotBlank
     * @Assert\DateTime()
     */
    private DateTimeImmutable $dateEnd;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $provider;

    /**
     * @ORM\ManyToOne(targetEntity="Ig0rbm\Memo\Entity\Account")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    private Account $account;

    /**
     * @throws Throwable
     */
    public static function createDefaultForAccount(Account $account): self
    {
        return new self(
            $account,
            new DateTimeImmutable(),
            new DateTimeImmutable(sprintf('+ %d months', License::DEFAULT_TERM)),
            License::PROVIDER_DEFAULT
        );
    }

    /**
     * @throws Throwable
     */
    public static function createPatreonLicenseForAccount(Account $account): self
    {
        return new self(
            $account,
            new DateTimeImmutable(),
            new DateTimeImmutable(sprintf('+ %d years', License::DEFAULT_TERM)),
            License::PROVIDER_PATREON
        );
    }

    public function __construct(
        Account $account,
        DateTimeImmutable $dateStart,
        DateTimeImmutable $dateEnd,
        string $provider
    ) {
        $this->account   = $account;
        $this->dateStart = $dateStart;
        $this->dateEnd   = $dateEnd;
        $this->provider  = $provider;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getDateStart(): DateTimeImmutable
    {
        return $this->dateStart;
    }

    public function setDateStart(DateTimeImmutable $dateStart): void
    {
        $this->dateStart = $dateStart;
    }

    public function getDateEnd(): DateTimeImmutable
    {
        return $this->dateEnd;
    }
    public function setDateEnd(DateTimeImmutable $dateEnd): void
    {
        $this->dateEnd = $dateEnd;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }
}
