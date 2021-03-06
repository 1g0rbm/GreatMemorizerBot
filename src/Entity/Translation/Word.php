<?php

namespace Ig0rbm\Memo\Entity\Translation;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="words", indexes={@ORM\Index(name="word_text", columns={"text"})})
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\Translation\WordRepository")
 */
class Word
{
    public const POS_NOUN = 'noun';
    public const POS_VERB = 'verb';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Assert\Type("integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=2)
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(min="2", max="2")
     */
    private string $langCode;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $text;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $pos;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Type("string")
     */
    private ?string $transcription = null;

    /**
     * @ORM\ManyToMany(targetEntity="Word", inversedBy="words", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="words2translation",
     *     joinColumns={@ORM\JoinColumn(name="word_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="translation_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @var Collection|Word[]
     */
    private Collection $translations;

    /**
     * @ORM\ManyToMany(targetEntity="Word", inversedBy="words", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="words2synonims",
     *     joinColumns={@ORM\JoinColumn(name="word_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="synonym_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @var Collection|Word[]
     */
    private Collection $synonyms;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->synonyms     = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
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
    public function getTranscription(): ?string
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
