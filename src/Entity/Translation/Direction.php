<?php

namespace Ig0rbm\Memo\Entity\Translation;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Ig0rbm\Memo\Validator\Constraints\Translation as AssertTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="directions",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="direction_idx", columns={"lang_from", "lang_to"})}
 * )
 * @ORM\Entity(repositoryClass="Ig0rbm\Memo\Repository\Translation\DirectionRepository")
 *
 * @AssertTranslation\DirectionLanguagesConstraint
 */
class Direction
{
    public const LANG_RU = 'ru';
    public const LANG_EN = 'en';

    public static array $availableLanguages = [self::LANG_RU, self::LANG_EN];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @Assert\NotBlank
     * @Assert\Type("integer")
     */
    private int $id;

    /**
     * @Assert\NotBlank;
     * @Assert\Type("string")
     * @AssertTranslation\DirectionAvailableLanguagesConstraint
     *
     * @ORM\Column(type="string", length=2)
     */
    private string $langFrom;

    /**
     * @Assert\NotBlank;
     * @Assert\Type("string")
     * @AssertTranslation\DirectionAvailableLanguagesConstraint
     *
     * @ORM\Column(type="string", length=2)
     */
    private string $langTo;

    /**
     * @Assert\NotBlank;
     * @Assert\Type("boolean")
     *
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private bool $isSavable = false;

    public function __construct(string $langFrom = Direction::LANG_EN, string $langTo = Direction::LANG_RU)
    {
        $this->langFrom = $langFrom;
        $this->langTo   = $langTo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getLangFrom(): string
    {
        return $this->langFrom;
    }

    public function setLangFrom(string $langFrom): void
    {
        $this->langFrom = $langFrom;
    }

    public function getLangTo(): string
    {
        return $this->langTo;
    }

    public function setLangTo(string $langTo): void
    {
        $this->langTo = $langTo;
    }

    public function getDirection(): string
    {
        return sprintf('%s-%s', $this->langFrom, $this->langTo);
    }

    public function isSavable(): bool
    {
        return $this->isSavable;
    }

    public function setIsSavable(bool $isSavable): void
    {
        $this->isSavable = $isSavable;
    }
}
