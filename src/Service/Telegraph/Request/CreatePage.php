<?php

namespace Ig0rbm\Memo\Service\Telegraph\Request;

use Ig0rbm\Memo\Entity\Telegraph\Content\AbstractElementNode;

class CreatePage extends BaseRequest
{
    /** @var string */
    protected $title;

    /** @var string */
    protected $authorName;

    /** @var string|null */
    protected $authorUrl;

    /** @var AbstractElementNode[] */
    protected $content;

    public static function rememberList(array $content): self
    {
        $request = new self();
        $request->setContent($content);
        $request->setTitle('Remember list');
        $request->setAuthorName('GreatMemoBot');

        return $request;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function setAuthorName(string $authorName): void
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

    public function getContent(): array
    {
        return $this->content;
    }

    public function setContent(array $content): void
    {
        $this->content = $content;
    }
}
