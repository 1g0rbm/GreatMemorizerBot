<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\WordList;

use Faker\Factory;
use Faker\Generator;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Telegraph\Content\ParagraphNode;
use Ig0rbm\Memo\Entity\Telegraph\Page;
use Ig0rbm\Memo\Entity\Translation\WordList;
use Ig0rbm\Memo\Exception\WordList\WordListException;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\Telegraph\ApiService;
use Ig0rbm\Memo\Service\Telegraph\Request\CreatePage;
use Ig0rbm\Memo\Service\Telegraph\Request\GetPage;
use Ig0rbm\Memo\Service\WordList\Telegraph\WordListBuilder;
use Ig0rbm\Memo\Service\WordList\WordListShowService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WordListShowServiceTest extends TestCase
{
    private WordListShowService $service;

    /** @var WordListRepository|MockObject */
    private WordListRepository $wordListRepository;

    /** @var AccountRepository|MockObject */
    private AccountRepository $accountRepository;

    /** @var ApiService|MockObject */
    private ApiService $apiService;

    /** @var WordListBuilder|MockObject */
    private WordListBuilder $builder;

    /** @var EntityFlusher|MockObject */
    private EntityFlusher $flusher;

    private Generator $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->wordListRepository = $this->createMock(WordListRepository::class);
        $this->accountRepository  = $this->createMock(AccountRepository::class);
        $this->apiService         = $this->createMock(ApiService::class);
        $this->builder            = $this->createMock(WordListBuilder::class);
        $this->flusher            = $this->createMock(EntityFlusher::class);

        $this->service = new WordListShowService(
            $this->wordListRepository,
            $this->accountRepository,
            $this->apiService,
            $this->builder,
            $this->flusher
        );
    }

    public function testFindByChatReturnUrlIfThereIsWordList(): void
    {
        $chat     = $this->createChat();
        $account  = $this->createAccount();
        $wordList = $this->createWordList();
        $page     = $this->createPage();

        $this->accountRepository->expects($this->once())
            ->method('findOneByChat')
            ->with($chat)
            ->willReturn($account);

        $this->apiService->expects($this->never())
            ->method('getPage');

        $this->wordListRepository->expects($this->once())
            ->method('findByChat')
            ->with($chat)
            ->willReturn($wordList);

        $p = new ParagraphNode();
        $this->builder->expects($this->once())
            ->method('build')
            ->with($wordList)
            ->willReturn($p);

        $this->apiService->expects($this->once())
            ->method('createPage')
            ->with(CreatePage::rememberList([$p]))
            ->willReturn($page);

        $result = $this->service->findByChat($chat);

        $this->assertEquals($page->getUrl(), $result);
    }

    public function testFindByChatReturnNullIfThereIsNoWordList(): void
    {
        $chat    = $this->createChat();
        $account = $this->createAccount();

        $this->accountRepository->expects($this->once())
            ->method('findOneByChat')
            ->with($chat)
            ->willReturn($account);

        $this->apiService->expects($this->never())
            ->method('getPage');

        $this->wordListRepository->expects($this->once())
            ->method('findByChat')
            ->willReturn(null);

        $result = $this->service->findByChat($chat);

        $this->assertNull($result);
    }

    public function testFindByChatThrowExceptionIfThereIsNotChat(): void
    {
        $chat = $this->createChat();

        $this->accountRepository->expects($this->once())
            ->method('findOneByChat')
            ->with($chat)
            ->willThrowException(WordListException::becauseThereIsNotListForChat($chat->getId()));

        $this->expectException(WordListException::class);

        $this->service->findByChat($chat);
    }

    public function testFindByChatReturnLinkIfThereIsPathInAccount(): void
    {
        $chat    = $this->createChat();
        $account = $this->createAccountWithPath();
        $page    = $this->createPage();

        $this->accountRepository->expects($this->once())
            ->method('findOneByChat')
            ->with($chat)
            ->willReturn($account);

        $getPage = new GetPage();
        $getPage->setPath($account->getPageListPath());

        $this->apiService->expects($this->once())
            ->method('getPage')
            ->with($getPage)
            ->willReturn($page);

        $string = $this->service->findByChat($chat);

        $this->assertEquals($string, $page->getUrl());
    }

    private function createWordList(): WordList
    {
        $list = new WordList();

        return $list;
    }

    private function createPage(): Page
    {
        $page = new Page();
        $page->setUrl('https://telegra.ph/test/path');
        $page->setPath('/test/path');

        return $page;
    }

    private function createAccountWithPath(): Account
    {
        $account = new Account();
        $account->setPageListPath('/test/path');

        return $account;
    }

    private function createAccount(): Account
    {
        return new Account();
    }

    private function createChat(): Chat
    {
        $chat = new Chat();
        $chat->setId(1);

        return $chat;
    }
}
