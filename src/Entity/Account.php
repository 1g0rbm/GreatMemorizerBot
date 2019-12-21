<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Validator\Constraints\Telegram\Message as TelegramMessageAssert;
use Ig0rbm\Memo\Validator\Constraints\Translation as AssertTranslation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="accounts")
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\AccountRepository")
 */
class Account
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
     * @Assert\NotBlank
     * @TelegramMessageAssert\Chat
     *
     * @ORM\OneToOne(targetEntity="Ig0rbm\Memo\Entity\Telegram\Message\Chat", cascade={"persist"})
     * @ORM\JoinColumn(name="chat_id", referencedColumnName="id")
     */
    private Chat $chat;

    /**
     * @Assert\NotBlank
     * @AssertTranslation\DirectionConstraint
     *
     * @ORM\ManyToOne(targetEntity="Ig0rbm\Memo\Entity\Translation\Direction", cascade={"persist"})
     * @ORM\JoinColumn(name="direction_id", referencedColumnName="id")
     */
    private Direction $direction;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Type("string")
     *
     * @var string|null
     */
    private ?string $pageListPath = null;

    /**
     * @ORM\Column(type="boolean", options={"default" = 0})
     *
     * @Assert\NotBlank
     * @Assert\Type("boolean")
     */
    private bool $needKeyboardUpdate = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getChat(): Chat
    {
        return $this->chat;
    }

    public function setChat(Chat $chat): void
    {
        $this->chat = $chat;
    }

    public function getDirection(): Direction
    {
        return $this->direction;
    }

    public function setDirection(Direction $direction): void
    {
        $this->direction = $direction;
    }

    public function getPageListPath(): ?string
    {
        return $this->pageListPath;
    }

    public function setPageListPath(?string $pageListPath): void
    {
        $this->pageListPath = $pageListPath;
    }

    public function isNeedKeyboardUpdate(): bool
    {
        return $this->needKeyboardUpdate;
    }

    public function setNeedKeyboardUpdate(bool $needKeyboardUpdate): void
    {
        $this->needKeyboardUpdate = $needKeyboardUpdate;
    }
}
