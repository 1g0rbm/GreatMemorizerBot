<?php

namespace Ig0rbm\Memo\Entity\Translation;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="words")
 */
class Word
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
     * @ORM\Column(type="string", length=2)
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(min="2", max="2")
     *
     * @var string
     */
    private $langCode;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     *
     * @var string
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     *
     * @var string
     */
    private $pos;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     *
     * @var string
     */
    private $transcription;

    /**
     * @ORM\ManyToMany(targetEntity="Word", inversedBy="words")
     * @ORM\JoinTable(
     *     name="words2translation",
     *     joinColumns={@ORM\JoinColumn(name="word_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="translation_id", referencedColumnName="id")}
     * )
     *
     * @var ArrayCollection|Word[]
     */
    private $translations;

    /**
     * @ORM\ManyToMany(targetEntity="Word", inversedBy="words")
     * @ORM\JoinTable(
     *     name="words2synonims",
     *     joinColumns={@ORM\JoinColumn(name="word_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="synonym_id", referencedColumnName="id")}
     * )
     *
     * @var ArrayCollection|Word[]
     */
    private $synonyms;

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
     * @return string
     */
    public function getLangCode(): string
    {
        return $this->langCode;
    }

    /**
     * @param string $langCode
     */
    public function setLangCode(string $langCode): void
    {
        $this->langCode = $langCode;
    }

    /**
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getPos(): string
    {
        return $this->pos;
    }

    /**
     * @param string $pos
     */
    public function setPos(string $pos): void
    {
        $this->pos = $pos;
    }

    /**
     * @return string
     */
    public function getTranscription(): string
    {
        return $this->transcription;
    }

    /**
     * @param string $transcription
     */
    public function setTranscription(string $transcription): void
    {
        $this->transcription = $transcription;
    }

    /**
     * @return ArrayCollection|Word[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param ArrayCollection|Word[] $translations
     */
    public function setTranslations($translations): void
    {
        $this->translations = $translations;
    }

    /**
     * @return ArrayCollection|Word[]
     */
    public function getSynonyms()
    {
        return $this->synonyms;
    }

    /**
     * @param ArrayCollection|Word[] $synonyms
     */
    public function setSynonyms($synonyms): void
    {
        $this->synonyms = $synonyms;
    }
}