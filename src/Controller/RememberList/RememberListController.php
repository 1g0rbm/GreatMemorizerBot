<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Controller\RememberList;

use Ig0rbm\Memo\Repository\Telegram\Message\ChatRepository;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\Telegram\TranslationMessageBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RememberListController extends AbstractController
{
    private WordListRepository $wordListRepository;

    private TranslationMessageBuilder $messageBuilder;

    public function __construct(WordListRepository $wordListRepository, TranslationMessageBuilder $messageBuilder)
    {
        $this->wordListRepository = $wordListRepository;
        $this->messageBuilder     = $messageBuilder;
    }

    /**
     * @Route("/bot/{wordListId}/list", name="remember_list", methods={"GET"})
     */
    public function showAction(int $wordListId): Response
    {

        $wordList = $this->wordListRepository->getOneById($wordListId);

        return $this->render('RememberList/show.html.twig', ['list' => $wordList->getWords()]);
    }
}
