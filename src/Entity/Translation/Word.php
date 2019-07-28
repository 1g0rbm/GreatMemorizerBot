<?php

namespace Ig0rbm\Memo\Entity\Translation;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="words", indexes={@ORM\Index(name="word_text", columns={"text"})})
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\Translation\WordRepository")
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
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Type("string")
     *
     * @var string
     */
    private $transcription;

    /**
     * @ORM\ManyToMany(targetEntity="Word", inversedBy="words", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="words2translation",
     *     joinColumns={@ORM\JoinColumn(name="word_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="translation_id", referencedColumnName="id")}
     * )
     *
     * @var Collection|Word[]
     */
    private $translations;

    /**
     * @ORM\ManyToMany(targetEntity="Word", inversedBy="words", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="words2synonims",
     *     joinColumns={@ORM\JoinColumn(name="word_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="synonym_id", referencedColumnName="id")}
     * )
     *
     * @var Collection|Word[]
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
     * @return null|Collection|Word[]
     */
    public function getTranslations(): ?Collection
    {
        return $this->translations;
    }

    /**
     * @param Collection|Word[] $translations
     */
    public function setTranslations($translations): void
    {
        $this->translations = $translations;
    }

    /**
     * @return null|Collection|Word[]
     */
    public function getSynonyms(): ?Collection
    {
        return $this->synonyms;
    }

    /**
     * @param Collection|Word[] $synonyms
     */
    public function setSynonyms($synonyms): void
    {
        $this->synonyms = $synonyms;
    }
}