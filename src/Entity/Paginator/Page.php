<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Paginator;

class Page
{
    public const FIRST_PAGE_NUM = 0;

    private int $page;

    private int $perPage;

    private int $totalPages;

    private int $currentItem;

    public function __construct(int $page, int $perPage, int $totalPages)
    {
        $this->page        = $page;
        $this->perPage     = $perPage;
        $this->totalPages  = $totalPages;
        $this->currentItem = $this->page * $this->perPage + 1;
    }

    public function getCurrentItem(): int
    {
        return $this->currentItem;
    }

    public function turnToNextItem(): void
    {
        $this->currentItem = ($this->totalPages * $this->perPage) === $this->currentItem ?
            $this->currentItem :
            $this->currentItem + 1;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function turnToNextPage(): self
    {
        $page = clone($this);

        $page->setCurrentPage($this->getNextPage());

        return $page;
    }

    public function turnToPrevPage(): self
    {
        $page = clone($this);

        $page->setCurrentPage($this->getPrevPage());

        return $page;
    }

    public function turnToLastPage(): self
    {
        $page = clone($this);

        $page->setCurrentPage($this->getLastPage());

        return $page;
    }

    public function turnToFirstPage(): self
    {
        $page = clone($this);

        $page->setCurrentPage($this->getFirstPage());

        return $page;
    }

    public function setCurrentPage(int $page): void
    {
        $this->page        = $page;
        $this->currentItem = $this->page * $this->perPage + 1;
    }

    public function getCurrentPage(): int
    {
        return $this->page;
    }

    public function getNextPage(): int
    {
        return $this->page < $this->getLastPage() ? $this->page + 1 : $this->page;
    }

    public function getPrevPage(): int
    {
        return $this->page > self::FIRST_PAGE_NUM ? $this->page - 1 : $this->page;
    }

    public function getFirstPage(): int
    {
        return self::FIRST_PAGE_NUM;
    }

    public function getLastPage(): int
    {
        return $this->totalPages - 1;
    }

    public function getFirstPageOffset(): int
    {
        return 0;
    }

    public function getLastPageOffset(): int
    {
        return $this->getLastPage() * $this->perPage;
    }

    public function getOffset(): int
    {
        return $this->getCurrentPage() * $this->perPage;
    }
}
