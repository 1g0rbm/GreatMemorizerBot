<?php

namespace Ig0rbm\Memo\Tests\Service\WordList\Telegraph;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Service\Telegraph\ApiService;
use Ig0rbm\Memo\Service\WordList\Telegraph\WordListManager;
use Ig0rbm\Memo\Service\WordList\Telegraph\WordListBuilder;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Telegraph\Content\ListNode;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Entity\Translation\WordList;
use Ig0rbm\Memo\Entity\Telegraph\Page;
use Ig0rbm\Memo\Service\Telegraph\Request\CreatePage;
use Ig0rbm\Memo\Service\Telegraph\Request\EditPage;

/**
 * @group unit
 * @group telegraph
 * @group wordList
 */
class WordListManagerUnitTest extends TestCase
{
    /** @var WordListManager */
    private $service;

    /** @var ApiService|MockObject */
    private $telegraphApi;

    /** @var WordListBuilder|MockObject */
    private $builder;

    /** @var AccountRepository|MockObject */
    private $accountRepository;

    /** @var EntityFlusher|MockObject */
    private $flusher;

    public function setUp(): void
    {
        parent::setUp();

        $this->telegraphApi = $this->createMock(ApiService::class);
        $this->builder = $this->createMock(WordListBuilder::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->flusher = $this->createMock(EntityFlusher::class);

        $this->service = new WordListManager(
            $this->telegraphApi,
            $this->builder,
            $this->accountRepository,
            $this->flusher
        );
    }

    public function testCreatePageIfThereIsNoPagePathInAccount(): void
    {
        $wordList     = $this->createWordList();
        $nodeList     = $this->createListNode();
        $account      = $this->createAccountWithoutPageListPath();
        $expectedPage = $this->createPage();

        $createPageRequest = new CreatePage();
        $createPageRequest->setContent([$nodeList]);
        $createPageRequest->setTitle('Remember list');
        $createPageRequest->setAuthorName('GreatMemoBot');

        $this->builder->expects($this->once())
            ->method('build')
            ->with($wordList)
            ->willReturn($nodeList);

        $this->accountRepository->expects($this->once())
            ->method('findOneByChat')
            ->with($wordList->getChat())
            ->willReturn($account);

        $this->telegraphApi->expects($this->once())
            ->method('createPage')
            ->with($createPageRequest)
            ->willReturn($expectedPage);

        $this->telegraphApi->expects($this->never())
            ->method('editPage');

        $this->flusher->expects($this->once())
            ->method('flush');

        $this->assertNull($account->getPageListPath());

        $page = $this->service->sendPage($wordList);

        $this->assertEquals($account->getPageListPath(), $page->getPath());
        $this->assertEquals($expectedPage, $page);
    }

    public function testCreatePageIfThereIsPagePathInAccount(): void
    {
        $wordList     = $this->createWordList();
        $nodeList     = $this->createListNode();
        $account      = $this->createAccountWithPageListPath();
        $expectedPage = $this->createPage();

        $editAccountRequest= new EditPage();
        $editAccountRequest->setContent([$nodeList]);
        $editAccountRequest->setTitle('Remember list');
        $editAccountRequest->setAuthorName('GreatMemoBot');
        $editAccountRequest->setPath($account->getPageListPath());

        $this->builder->expects($this->once())
            ->method('build')
            ->with($wordList)
            ->willReturn($nodeList);

        $this->accountRepository->expects($this->once())
            ->method('findOneByChat')
            ->with($wordList->getChat())
            ->willReturn($account);

        $this->telegraphApi->expects($this->once())
            ->method('editPage')
            ->with($editAccountRequest)
            ->willReturn($expectedPage);

        $this->telegraphApi->expects($this->never())
            ->method('createPage');

        $this->flusher->expects($this->never())
            ->method('flush');

        $page = $this->service->sendPage($wordList);

        $this->assertEquals($account->getPageListPath(), $page->getPath());
        $this->assertEquals($expectedPage, $page);
    }

    private function createPage(): Page
    {
        $page = new Page();
        $page->setPath('/test/path');

        return $page;
    }

    private function createAccountWithoutPageListPath(): Account
    {
        $account = new Account;

        return $account;
    }

    private function createAccountWithPageListPath(): Account
    {
        $account = new Account;
        $account->setPageListPath('/test/path');

        return $account;
    }

    private function createListNode(): ListNode
    {
        $ul = new ListNode();

        return $ul;
    }

    private function createWordList(): WordList
    {
        $word1 = new Word();
        $word1->setText('to test');
        $word1->setPos('verb');

        $word2 = new Word();
        $word2->setText('test');
        $word2->setPos('noun');

        $chat = new Chat();
        $wordList = new WordList();
        $collection = new ArrayCollection();

        $wordList->setChat($chat);

        $collection->set($word1->getPos(), $word1);
        $collection->set($word2->getPos(), $word2);

        $wordList->setWords($collection);

        return $wordList;
    }
}
