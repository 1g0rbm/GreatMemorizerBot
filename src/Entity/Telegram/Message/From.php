<?php


namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Symfony\Component\Validator\Constraints as Assert;

class From
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("integer")
     *
     * @var integer
     */
    private $id;

    /**
     * @Assert\NotNull
     * @Assert\Type("boolean")
     *
     * @var bool
     */
    private $isBot;

    /**
     * @Assert\Type("string")
     *
     * @var string|null
     */
    private $firstName;

    /**
     * @Assert\Type("string")
     *
     * @var string|null
     */
    private $lastName;

    /**
     * @Assert\Type("string")
     *
     * @var string|null
     */
    private $username;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     *
     * @var string
     */
    private $languageCode;

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
     * @return bool
     */
    public function isBot(): bool
    {
        return $this->isBot;
    }

    /**
     * @param bool $isBot
     */
    public function setIsBot(bool $isBot): void
    {
        $this->isBot = $isBot;
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
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    /**
     * @param string $languageCode
     */
    public function setLanguageCode(string $languageCode): void
    {
        $this->languageCode = $languageCode;
    }
}