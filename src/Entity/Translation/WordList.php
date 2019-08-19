<?php

namespace Ig0rbm\Memo\Entity\Translation;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Validator\Constraints\Telegram\Message as TelegramMessageAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="word_lists")
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\Translation\WordListRepository")
 */
class WordList
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
     * @Assert\NotBlank
     * @TelegramMessageAssert\Chat
     *
     * @ORM\OneToOne(targetEntity="Ig0rbm\Memo\Entity\Telegram\Message\Chat")
     * @ORM\JoinColumn(name="chat_id", referencedColumnName="id")
     *
     * @var Chat
     */
    private $chat;

    /**
     * @ORM\ManyToMany(targetEntity="Ig0rbm\Memo\Entity\Translation\Word", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="lists2words",
     *     joinColumns={@ORM\JoinColumn(name="word_list_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="word_id", referencedColumnName="id")}
     * )
     *
     * @var Collection|Word[]
     */
    private $words;

    public function __construct()
    {
        $this->words = new ArrayCollection();
    }

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
     * @return Chat
     */
    public function getChat(): Chat
    {
        return $this->chat;
    }

    /**
     * @param Chat $chat
     */
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
}
