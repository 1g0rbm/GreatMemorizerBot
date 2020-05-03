<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Quiz;

use Doctrine\ORM\Mapping as ORM;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Validator\Constraints\Telegram\Message as TelegramMessageAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="quiz_reminder",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="chat_reminder_quiz", columns={"chat_id", "time"})}
 * )
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\Quiz\QuizReminderRepository")
 */
class QuizReminder
{
    public const TYPE_ENABLE = 'enable';

    public const TYPE_DISABLE = 'disable';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
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
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $time;

    /**
     * @ORM\Column(type="string", options={"default": QuizReminder::TYPE_ENABLE})
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $status = self::TYPE_ENABLE;

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

    public function getTime(): string
    {
        return $this->time;
    }

    public function setTime(string $time): void
    {
        $this->time = $time;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
