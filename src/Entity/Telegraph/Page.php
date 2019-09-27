<?php

namespace Ig0rbm\Memo\Entity\Telegraph;

use Ig0rbm\Memo\Entity\Telegraph\Content\AbstractElementNode;

class Page
{
    /** @var string */
    private $path;

    /** @var string */
    private $url;

    /** @var string */
    private $title;

    /** @var string */
    private $description;

    /** @var string|null */
    private $authorName;

    /** @var string|null */
    private $authorUrl;

    /** @var string|null */
    private $imageUrl;

    /** @var AbstractElementNode[]*/
    private $content;

    /** @var integer */
    private $views;

    /** @var bool */
    private $canEdit;

    public static function createFromTelegraphResponse(array $arr): self
    {
        $page = new self();
        $page->setPath($arr['path']);
        $page->setUrl($arr['url']);
        $page->setTitle($arr['title']);
        $page->setDescription($arr['description']);
        $page->setAuthorName($arr['author_name']);
        $page->setViews($arr['views']);

        if (isset($arr['content'])) {
            $page->setContent($arr['content']);
        }

        if (isset($arr['can_edit'])) {
            $page->setCanEdit($arr['can_edit']);
        }

        return $page;
    }

    public function __construct()
    {
        $this->views = 0;
        $this->canEdit = false;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    public function setAuthorName(?string $authorName): void
    {
        $this->authorName = $authorName;
    }

    public function getAuthorUrl(): ?string
    {
        return $this->authorUrl;
    }

    public function setAuthorUrl(?string $authorUrl): void
    {
        $this->authorUrl = $authorUrl;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function setContent(array $content): void
    {
        $this->content = $content;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function setViews(int $views): void
    {
        $this->views = $views;
    }

    public function isCanEdit(): bool
    {
        return $this->canEdit;
    }

    public function setCanEdit(bool $canEdit): void
    {
        $this->canEdit = $canEdit;
    }
}
