<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Watch;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Validator\Constraints\Telegram\Message as TelegramMessageAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_action_log")
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\Watch\ClientActionLogRepository")
 */
class ClientActionLog
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @Assert\Type("integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ig0rbm\Memo\Entity\Telegram\Message\Chat")
     * @ORM\JoinColumn(name="chat_id", referencedColumnName="id")
     *
     * @Assert\NotBlank
     * @TelegramMessageAssert\Chat
     */
    private Chat $chat;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Type("string")
     */
    private ?string $command;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Type("string")
     */
    private ?string $text;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default": "CURRENT_TIMESTAMP"})
     *
     * @Assert\NotBlank
     * @Assert\DateTime()
     */
    private DateTimeImmutable $dateTime;

    public function __construct(Chat $chat, ?string $command, ?string $text)
    {
        $this->chat     = $chat;
        $this->command  = $command;
        $this->text     = $text;
        $this->dateTime = new DateTimeImmutable();
    }

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

    public function getCommand(): ?string
    {
        return $this->command;
    }

    public function setCommand(?string $command): void
    {
        $this->command = $command;
    }

    public function getDateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function setDateTime(DateTimeImmutable $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }
}
