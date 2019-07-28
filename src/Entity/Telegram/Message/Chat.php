<?php


namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="chats")
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\Telegram\Message\ChatRepository")
 */
class Chat
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     * @Assert\Type("integer")
     *
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Type("string")
     *
     * @var string|null
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Type("string")
     *
     * @var string|null
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Type("string")
     *
     * @var string|null
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     *
     * @var string
     */
    private $type;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}