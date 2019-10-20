<?php

namespace Ig0rbm\Memo\Entity\Quiz;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Ig0rbm\Memo\Validator\Constraints\Telegram\Message as TelegramMessageAssert;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz")
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\Quiz\QuizRepository")
 */
class Quiz
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     * @Assert\Type("integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ig0rbm\Memo\Entity\Telegram\Message\Chat")
     * @ORM\JoinColumn(name="chat_id", referencedColumnName="id")
     *
     * @Assert\NotBlank
     * @TelegramMessageAssert\Chat
     *
     * @var Chat
     */
    private $chat;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     * @Assert\Type("integer")
     *
     * @var int
     */
    private $length;

    /**
     * @var QuizStep[]|Collection
     *
     * @ORM\OneToMany(targetEntity="QuizStep", mappedBy="quiz", cascade={"persist"})
     */
    private $steps;

    public function __construct()
    {
        $this->steps = new ArrayCollection();
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

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length): void
    {
        $this->length = $length;
    }

    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function setSteps(ArrayCollection $steps): void
    {
        $this->steps = $steps;
    }
}
