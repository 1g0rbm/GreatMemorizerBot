<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Quiz;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ig0rbm\Memo\Entity\Translation\Word;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="quiz_steps",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="step_correct_word", columns={"quiz_id", "correct_word_id"})}
 * )
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\Quiz\QuizStepRepository")
 */
class QuizStep
{
    public const DEFAULT_LENGTH = 4;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Assert\Type("integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Quiz", inversedBy="steps")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     *
     * @Assert\NotBlank
     */
    private Quiz $quiz;

    /**
     * @ORM\ManyToOne(targetEntity="Ig0rbm\Memo\Entity\Translation\Word", cascade={"persist"})
     * @ORM\JoinColumn(name="correct_word_id", referencedColumnName="id")
     *
     * @Assert\NotBlank
     */
    private Word $correctWord;

    /**
     * @ORM\ManyToMany(targetEntity="Ig0rbm\Memo\Entity\Translation\Word", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="quiz_step2words",
     *     joinColumns={@ORM\JoinColumn(name="quiz_step_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="word_id", referencedColumnName="id")}
     * )
     *
     * @Assert\NotBlank
     *
     * @var Word[]|Collection
     */
    private Collection $words;

    /**
     * @ORM\Column(type="integer", options={"default": QuizStep::DEFAULT_LENGTH})
     *
     * @Assert\NotBlank
     * @Assert\Type("integer")
     */
    private int $length;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Type("string")
     */
    private ?string $answer = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isAnswered = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isCorrect = false;

    public function __construct(Quiz $quiz)
    {
        $this->words  = new ArrayCollection();
        $this->length = self::DEFAULT_LENGTH;
        $this->quiz   = $quiz;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(Quiz $quiz): void
    {
        $this->quiz = $quiz;
    }

    public function getCorrectWord(): Word
    {
        return $this->correctWord;
    }

    public function setCorrectWord(Word $correctWord): void
    {
        $this->correctWord = $correctWord;
    }

    /**
     * @return Collection|Word[]
     */
    public function getWords(): Collection
    {
        return $this->words;
    }

    public function setWords(Collection $wrongWords): void
    {
        $this->words = $wrongWords;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length): void
    {
        $this->length = $length;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): void
    {
        $this->answer = $answer;
    }

    public function isAnswered(): bool
    {
        return $this->isAnswered;
    }

    public function setIsAnswered(bool $isAnswered): void
    {
        $this->isAnswered = $isAnswered;
    }

    public function isCorrect(): bool
    {
        return $this->isCorrect;
    }

    public function setIsCorrect(bool $isCorrect): void
    {
        $this->isCorrect = $isCorrect;
    }
}
