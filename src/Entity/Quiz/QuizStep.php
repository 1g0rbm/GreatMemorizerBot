<?php

namespace Ig0rbm\Memo\Entity\Quiz;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ig0rbm\Memo\Entity\Translation\Word;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz_steps")
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\Quiz\QuizStepRepository")
 */
class QuizStep
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
     * @var Quiz
     *
     * @ORM\ManyToOne(targetEntity="Quiz", inversedBy="steps")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     *
     * @Assert\NotBlank
     */
    private $quiz;

    /**
     * @var Word
     *
     * @ORM\OneToOne(targetEntity="Ig0rbm\Memo\Entity\Translation\Word", cascade={"persist"})
     * @ORM\JoinColumn(name="correct_word_id", referencedColumnName="id")
     *
     * @Assert\NotBlank
     */
    private $correctWord;

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
    private $words;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $isAnswered;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $isCorrect;

    public function __construct()
    {
        $this->words = new ArrayCollection();
        $this->isAnswered = false;
        $this->isCorrect  = false;
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

    public function getWords(): Collection
    {
        return $this->words;
    }

    public function setWords(Collection $wrongWords): void
    {
        $this->words = $wrongWords;
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
