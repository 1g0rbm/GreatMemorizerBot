<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Paginator;

use Ig0rbm\Memo\Entity\Paginator\Page;
use Ig0rbm\Memo\Service\Paginator\Paginator;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class PaginatorUnitTest extends TestCase
{
    private Paginator $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new Paginator();
    }

    public function testDoReturnFirstPageIfActionToFirstPage(): void
    {
        $page = $this->createPage();

        $page = $this->service->do('<<', $page);

        $this->assertEquals(0, $page->getCurrentPage());
        $this->assertEquals(1, $page->getNextPage());
        $this->assertEquals(0, $page->getPrevPage());
    }

    public function testDoReturnLastPageIfActionToLastPage(): void
    {
        $page = $this->createPage();

        $page = $this->service->do('>>', $page);

        $this->assertEquals(9, $page->getCurrentPage());
        $this->assertEquals(9, $page->getNextPage());
        $this->assertEquals(8, $page->getPrevPage());
    }

    public function testDoReturnNextIfActionTurnNextPage(): void
    {
        $page = $this->createPage();

        $page = $this->service->do('>', $page);

        $this->assertEquals(0, $page->getCurrentPage());
        $this->assertEquals(1, $page->getNextPage());
        $this->assertEquals(0, $page->getPrevPage());
    }

    private function createPage(): Page
    {
        return new Page(0, 5, 10);
    }
}
