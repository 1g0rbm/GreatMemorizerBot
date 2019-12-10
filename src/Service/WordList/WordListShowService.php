<?php

namespace Ig0rbm\Memo\Service\WordList;

use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Exception\WordList\WordListException;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\Telegraph\ApiService;
use Ig0rbm\Memo\Service\Telegraph\Request\CreatePage;
use Ig0rbm\Memo\Service\Telegraph\Request\GetPage;
use Ig0rbm\Memo\Service\WordList\Telegraph\WordListBuilder;

class WordListShowService
{
    private WordListRepository $wordListRepository;

    private AccountRepository $accountRepository;

    private ApiService $apiService;

    private WordListBuilder $builder;

    private EntityFlusher $flusher;

    public function __construct(
        WordListRepository $wordListRepository,
        AccountRepository $accountRepository,
        ApiService $apiService,
        WordListBuilder $builder,
        EntityFlusher $flusher
    ) {
        $this->wordListRepository = $wordListRepository;
        $this->accountRepository  = $accountRepository;
        $this->apiService         = $apiService;
        $this->builder            = $builder;
        $this->flusher            = $flusher;
    }

    public function findByChat(Chat $chat): ?string
    {
        $account = $this->accountRepository->findOneByChat($chat);
        if ($account === null) {
            throw WordListException::becauseThereIsNotAccountForChat($chat);
        }

        if ($account->getPageListPath()) {
            $getPageRequest = new GetPage();
            $getPageRequest->setPath($account->getPageListPath());

            $page = $this->apiService->getPage($getPageRequest);

            return $page->getUrl();
        }

        $wordList = $this->wordListRepository->findByChat($chat);
        if ($wordList === null) {
            return null;
        }

        $page = $this->apiService->createPage(CreatePage::rememberList([$this->builder->build($wordList)]));

        $account->setPageListPath($page->getPath());
        $this->flusher->flush();

        return $page->getUrl();
    }
}
