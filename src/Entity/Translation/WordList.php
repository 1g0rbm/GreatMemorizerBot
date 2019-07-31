<?php

namespace Ig0rbm\Memo\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Validator\Constraints\Telegram\Message as TelegramMessageAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="word_list")
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\Translation\WordListRepository")
 */
class WordList
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     * @Assert\Type("integer")
     *
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Ig0rbm\Memo\Entity\Telegram\Message\Chat", inversedBy="chats")
     * @ORM\JoinColumn(name="chat_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @Assert\NotBlank
     * @TelegramMessageAssert\Chat
     *
     * @var Chat
     */
    private $chat;

    /**
     * @ORM\ManyToOne(targetEntity="Word", inversedBy="words")
     * @ORM\JoinColumn(name="word_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var Collection|Word[]
     */
    private $words;

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
    public function getWords()
    {
        return $this->words;
    }

    /**
     * @param Collection|Word[] $words
     */
    public function setWords($words): void
    {
        $this->words = $words;
    }
}