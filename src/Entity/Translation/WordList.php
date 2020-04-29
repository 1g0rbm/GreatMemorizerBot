<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Translation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Validator\Constraints\Telegram\Message as TelegramMessageAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="word_lists")
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\Translation\WordListRepository")
 */
class WordList
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
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
     * @ORM\ManyToMany(targetEntity="Ig0rbm\Memo\Entity\Translation\Word", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="lists2words",
     *     joinColumns={@ORM\JoinColumn(name="word_list_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="word_id", referencedColumnName="id")}
     * )
     */
    private Collection $words;

    public function __construct()
    {
        $this->words = new ArrayCollection();
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

    /**
     * @return Collection|Word[]
     */
    public function getWords(): Collection
    {
        return $this->words;
    }

    /**
     * @param Collection|Word[] $words
     */
    public function setWords(Collection $words): void
    {
        $this->words = $words;
    }

    public function addWord(Word $word): void
    {
        $this->words->add($word);
    }

    public function containsWord(Word $word): bool
    {
        return $this->words->contains($word);
    }
}
