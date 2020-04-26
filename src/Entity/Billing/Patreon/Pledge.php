<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Billing\Patreon;

use Doctrine\ORM\Mapping as ORM;
use Ig0rbm\Memo\Entity\Account;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="pledges",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="pledge_email_unique", columns={"email"})}
 * )
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\Billing\Patreon\PledgeRepository")
 */
class Pledge
{
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
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank
     * @Assert\Email
     */
    private string $email;

    /**
     * @ORM\OneToOne(targetEntity="Ig0rbm\Memo\Entity\Account", cascade={"persist"})
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    private ?Account $account = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): void
    {
        $this->account = $account;
    }
}
