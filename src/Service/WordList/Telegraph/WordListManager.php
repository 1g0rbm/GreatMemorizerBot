<?php

namespace Ig0rbm\Memo\Service\WordList\Telegraph;

use Ig0rbm\Memo\Entity\Telegraph\Page;
use Ig0rbm\Memo\Entity\Translation\WordList;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\Telegraph\ApiService;
use Ig0rbm\Memo\Service\Telegraph\Request\CreatePage;
use Ig0rbm\Memo\Service\Telegraph\Request\EditPage;

class WordListManager
{
    /** @var ApiService */
    private $telegraphApi;

    /** @var WordListBuilder */
    private $builder;

    /** @var AccountRepository */
    private $accountRepository;
    /**
     * @var EntityFlusher
     */
    private $flusher;

    public function __construct(
        ApiService $telegraphApi,
        WordListBuilder $builder,
        AccountRepository $accountRepository,
        EntityFlusher $flusher
    ) {
        $this->telegraphApi      = $telegraphApi;
        $this->builder           = $builder;
        $this->accountRepository = $accountRepository;
        $this->flusher           = $flusher;
    }

    public function sendPage(WordList $wordList): Page
    {
        $list    = $this->builder->build($wordList);
        $account = $this->accountRepository->findOneByChat($wordList->getChat());

        if ($account->getPageListPath() === null) {
            $createPageRequest = new CreatePage();
            $createPageRequest->setContent([$list]);
            $createPageRequest->setTitle('Remember list');
            $createPageRequest->setAuthorName('GreatMemoBot');

            $page = $this->telegraphApi->createPage($createPageRequest);
            $account->setPageListPath($page->getPath());
            $this->flusher->flush();
        } else {
            $updatePageRequest = new EditPage();
            $updatePageRequest->setAuthorName('GreatMemoBot');
            $updatePageRequest->setTitle('Remember list');
            $updatePageRequest->setPath($account->getPageListPath());
            $updatePageRequest->setContent([$list]);

            $page = $this->telegraphApi->editPage($updatePageRequest);
        }

        return $page;
    }
}
