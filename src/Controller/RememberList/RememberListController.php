<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Controller\RememberList;

use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\Telegram\TranslationMessageBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function strtoupper;

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

        $list = [];
        foreach ($wordList->getWords() as $word) {
            $list[$word->getText()]['source']        = strtoupper($word->getText());
            $list[$word->getText()]['transcription'] = $word->getTranscription();

            $translation = $word->getTranslations()
                ->map(static function (Word $word) {
                    $string = array_merge(
                        $word->getSynonyms()->map(static fn (Word $word) => $word->getText())->toArray(),
                        [$word->getText()]
                    );

                    return implode(', ', $string);
                })
                ->toArray();

            $list[$word->getText()]['words'][] = [
                'pos' => $word->getPos(),
                'translation' => implode(', ', $translation)
            ];
        }

        return $this->render('RememberList/show.html.twig', ['list' => $list]);
    }
}
