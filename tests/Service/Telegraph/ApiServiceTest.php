<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Telegraph;

use Ig0rbm\Memo\Entity\Telegraph\Account;
use Ig0rbm\Memo\Entity\Telegraph\Content\ListItemNode;
use Ig0rbm\Memo\Entity\Telegraph\Content\ListNode;
use Ig0rbm\Memo\Entity\Telegraph\Content\ParagraphNode;
use Ig0rbm\Memo\Entity\Telegraph\Page;
use Ig0rbm\Memo\Service\Telegraph\ApiService;
use Ig0rbm\Memo\Service\Telegraph\Request\CreatePage;
use Ig0rbm\Memo\Service\Telegraph\Request\EditPage;
use Ig0rbm\Memo\Service\Telegraph\Request\GetAccount;
use Ig0rbm\Memo\Service\Telegraph\Request\GetPage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group functional
 * @group telegraph
 */
class   ApiServiceTest extends WebTestCase
{
    private ApiService $service;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->service = self::$container->get(ApiService::class);
    }

    public function testGetAccountDefaultInfo(): void
    {
        $request = new GetAccount();

        $response = $this->service->getAccountInfo($request);

        $this->assertInstanceOf(Account::class, $response);
        $this->assertEquals('DevMemoBot', $response->getShortName());
        $this->assertEquals('DevGreatMemorizerBot', $response->getAuthorName());
    }

    public function testGetAccountCustomInfo(): void
    {
        $request = new GetAccount();
        $request->setFields([GetAccount::FIELD_AUTHOR_NAME]);

        $response = $this->service->getAccountInfo($request);

        $this->assertInstanceOf(Account::class, $response);
        $this->assertEquals('DevGreatMemorizerBot', $response->getAuthorName());
    }

    public function testCreatePage(): void
    {
        $title      = 'Test title';
        $authorName = 'DevMemoBot';

        $p = new ParagraphNode();
        $p->setText('Hello, World');

        $li1 = new ListItemNode();
        $li1->setText('item 1');

        $li2 = new ListItemNode();
        $li2->setText('item 2');

        $l = new ListNode();
        $l->addChild($li1);
        $l->addChild($li2);

        $request = new CreatePage();
        $request->setTitle($title);
        $request->setAuthorName($authorName);
        $request->setContent([$p, $l]);

        $response = $this->service->createPage($request);

        $this->assertInstanceOf(Page::class, $response);
        $this->assertEquals($title, $response->getTitle());
        $this->assertEquals($authorName, $response->getAuthorName());
        $this->assertNull($response->getAuthorUrl());
    }

    public function testEditPage(): void
    {
        $title      = 'Test title';
        $authorName = 'DevMemoBot';

        $p = new ParagraphNode();
        $p->setText('Hello, World');

        $li1 = new ListItemNode();
        $li1->setText('item 1');

        $li2 = new ListItemNode();
        $li2->setText('item 2');

        $l = new ListNode();
        $l->addChild($li1);
        $l->addChild($li2);

        $request = new CreatePage();
        $request->setTitle($title);
        $request->setAuthorName($authorName);
        $request->setContent([$p, $l]);

        $createdPage = $this->service->createPage($request);

        $this->assertInstanceOf(Page::class, $createdPage);
        $this->assertEquals($title, $createdPage->getTitle());
        $this->assertEquals($authorName, $createdPage->getAuthorName());
        $this->assertNull($createdPage->getAuthorUrl());

        $title = 'Edit title';

        $request = new EditPage();
        $request->setTitle($title);
        $request->setAuthorName($authorName);
        $request->setContent([$p, $l]);
        $request->setPath($createdPage->getPath());

        $editedPage = $this->service->editPage($request);

        $this->assertInstanceOf(Page::class, $editedPage);
        $this->assertEquals($title, $editedPage->getTitle());

        $this->assertNotEquals($createdPage->getTitle(), $editedPage->getTitle());
        $this->assertEquals($createdPage->getPath(), $editedPage->getPath());
    }

    public function testGetPage(): void
    {
        $title      = 'Test title';
        $authorName = 'DevMemoBot';

        $p = new ParagraphNode();
        $p->setText('Hello, World');

        $li1 = new ListItemNode();
        $li1->setText('item 1');

        $li2 = new ListItemNode();
        $li2->setText('item 2');

        $l = new ListNode();
        $l->addChild($li1);
        $l->addChild($li2);

        $request = new CreatePage();
        $request->setTitle($title);
        $request->setAuthorName($authorName);
        $request->setContent([$p, $l]);

        $response = $this->service->createPage($request);

        $this->assertInstanceOf(Page::class, $response);
        $this->assertEquals($authorName, $response->getAuthorName());
        $this->assertNull($response->getAuthorUrl());

        $getPageRequest = new GetPage();

        $getPageRequest->setPath($response->getPath());

        $getPageResponse = $this->service->getPage($getPageRequest);

        $this->assertInstanceOf(Page::class, $getPageResponse);
    }
}
