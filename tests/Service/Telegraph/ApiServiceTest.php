<?php

namespace Ig0rbm\Memo\Tests\Service\Telegraph;

use Ig0rbm\Memo\Service\Telegraph\Request\GetAccount;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Ig0rbm\Memo\Entity\Telegraph\Content\ListItemNode;
use Ig0rbm\Memo\Entity\Telegraph\Content\ListNode;
use Ig0rbm\Memo\Entity\Telegraph\Content\ParagraphNode;
use Ig0rbm\Memo\Entity\Telegraph\Page;
use Ig0rbm\Memo\Service\Telegraph\Request\CreatePage;
use Ig0rbm\Memo\Service\Telegraph\ApiService;

/**
 * @group functional
 * @group telegraph
 */
class ApiServiceTest extends WebTestCase
{
    /** @var ApiService */
    private $service;

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

        $this->assertTrue($response['ok']);
        $this->assertEquals('DevMemoBot', $response['result']['short_name']);
        $this->assertEquals('DevGreatMemorizerBot', $response['result']['author_name']);
    }

    public function testGetAccountCustomInfo(): void
    {
        $request = new GetAccount();
        $request->setFields([GetAccount::FIELD_AUTHOR_NAME]);

        $response = $this->service->getAccountInfo($request);

        $this->assertTrue($response['ok']);
        $this->assertEquals('DevGreatMemorizerBot', $response['result']['author_name']);
        $this->assertEquals(1, count($response['result']));
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
}
