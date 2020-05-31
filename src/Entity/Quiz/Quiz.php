<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Quiz;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Translation\WordList;
use Ig0rbm\Memo\Exception\Quiz\CreateQuizException;
use Ig0rbm\Memo\Validator\Constraints\Telegram\Message as TelegramMessageAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz")
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\Quiz\QuizRepository")
 */
class Quiz
{
    public const FROM_WORD_LIST = 'from_word_list';
    public const FROM_ALL       = 'from_all';

    public const TYPES = [
        self::FROM_WORD_LIST,
        self::FROM_ALL,
    ];

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
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     * @Assert\Type("integer")
     */
    private int $length = 5;

    /**
     * @var QuizStep[]|Collection
     *
     * @ORM\OneToMany(targetEntity="QuizStep", mappedBy="quiz", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private Collection $steps;

    /**
     * @ORM\OneToOne(targetEntity="QuizStep")
     * @ORM\JoinColumn(name="current_step_id", referencedColumnName="id")
     */
    private ?QuizStep $currentStep = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isComplete = false;

    /**
     * @ORM\ManyToOne(targetEntity="Ig0rbm\Memo\Entity\Translation\WordList")
     * @ORM\JoinColumn(name="word_list_id", referencedColumnName="id")
     */
    private ?WordList $wordList = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $wordListId = null;

    /**
     * @ORM\Column(type="string", length=50, options={"default": Quiz::FROM_WORD_LIST})
     *
     * @Assert\Type("string")
     */
    private string $type;

    public function __construct()
    {
        $this->steps = new ArrayCollection();
        $this->type  = self::FROM_ALL;
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

    /**
     * @return Collection|QuizStep[]
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function setSteps(Collection $steps): void
    {
        $this->steps = $steps;
    }

    public function getCurrentStep(): ?QuizStep
    {
        return $this->currentStep;
    }

    public function setCurrentStep(?QuizStep $currentStep): void
    {
        $this->currentStep = $currentStep;
    }

    public function isComplete(): bool
    {
        return $this->isComplete;
    }

    public function setIsComplete(bool $isComplete): void
    {
        $this->isComplete = $isComplete;
    }

    public function getWordListId(): ?int
    {
        return $this->wordListId;
    }

    public function setWordListId(?int $wordListId): void
    {
        $this->wordListId = $wordListId;
    }

    public function getWordList(): ?WordList
    {
        return $this->wordList;
    }

    public function setWordList(?WordList $wordList): void
    {
        $this->wordList = $wordList;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        if (!in_array($type, self::TYPES)) {
            throw CreateQuizException::wrongType($type);
        }

        $this->type = $type;
    }
}
