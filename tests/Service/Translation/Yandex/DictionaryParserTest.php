<?php

namespace Ig0rbm\Memo\Tests\Service\Yandex;

use Doctrine\Common\Collections\ArrayCollection;
use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Entity\Translation\Direction;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Service\Translation\Yandex\DictionaryParser;

class DictionaryParserTest extends TestCase
{
    /** @var DictionaryParser */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new DictionaryParser();
    }

    public function testParseReturnWordsCollection(): void
    {
        $dictionary = $this->getDictionaryWithNounAndAdjective();
        $direction = $this->getDirection();

        $words = $this->service->parse(json_encode($dictionary), $direction);
        $this->assertInstanceOf(HandyBag::class, $words);
        $this->assertSame(count($dictionary['def']), $words->count());

        /** @var Word $noun */
        $noun = $words->get('noun');
        $this->assertSame($dictionary['def'][0]['text'], $noun->getText());
        $this->assertSame($dictionary['def'][0]['pos'], $noun->getPos());
        $this->assertSame($dictionary['def'][0]['ts'], $noun->getTranscription());
        $this->assertSame($direction->getLangFrom(), $noun->getLangCode());

        /** @var Word $adjective */
        $adjective = $words->get('adjective');
        $this->assertSame($dictionary['def'][1]['text'], $adjective->getText());
        $this->assertSame($dictionary['def'][1]['pos'], $adjective->getPos());
        $this->assertSame($dictionary['def'][1]['ts'], $adjective->getTranscription());
        $this->assertSame($direction->getLangFrom(), $adjective->getLangCode());

        $tr = $dictionary['def'][0]['tr'];
        $this->assertInstanceOf(ArrayCollection::class, $noun->getTranslations());
        /** @var Word $translation */
        $translation = $noun->getTranslations()->first();
        $this->assertSame($tr[0]['text'], $translation->getText());
        $this->assertSame($tr[0]['pos'], $translation->getPos());
        $this->assertSame($direction->getLangTo(), $translation->getLangCode());

        $syn = $tr[0]['syn'];
        $this->assertInstanceOf(ArrayCollection::class, $translation->getSynonyms());
        /** @var Word $synonym */
        $synonym = $translation->getSynonyms()->first();
        $this->assertSame($syn[0]['text'], $synonym->getText());
        $this->assertSame($syn[0]['pos'], $synonym->getPos());
        $this->assertSame($direction->getLangTo(), $synonym->getLangCode());
    }

    public function testParseWhenUnclearPosReturnWordsCollection(): void
    {
        $dictionary = $this->getDictionaryWithAdverbAndUnclear();
        $direction = $this->getDirection();

        $words = $this->service->parse(json_encode($dictionary), $direction);
        $this->assertInstanceOf(HandyBag::class, $words);
        $this->assertSame(count($dictionary['def']), $words->count());

        /** @var Word $noun */
        $noun = $words->get('adverb');
        $this->assertSame($dictionary['def'][0]['text'], $noun->getText());
        $this->assertSame($dictionary['def'][0]['pos'], $noun->getPos());
        $this->assertSame($dictionary['def'][0]['ts'], $noun->getTranscription());
        $this->assertSame($direction->getLangFrom(), $noun->getLangCode());

        /** @var Word $adjective */
        $adjective = $words->get('unclear');
        $this->assertSame($dictionary['def'][1]['text'], $adjective->getText());
        $this->assertFalse(isset($dictionary['def'][1]['pos']));
        $this->assertSame('unclear', $adjective->getPos());
        $this->assertSame($dictionary['def'][1]['ts'], $adjective->getTranscription());
        $this->assertSame($direction->getLangFrom(), $adjective->getLangCode());

        $tr = $dictionary['def'][0]['tr'];
        $this->assertInstanceOf(ArrayCollection::class, $noun->getTranslations());
        /** @var Word $translation */
        $translation = $noun->getTranslations()->first();
        $this->assertSame($tr[0]['text'], $translation->getText());
        $this->assertSame($tr[0]['pos'], $translation->getPos());
        $this->assertSame($direction->getLangTo(), $translation->getLangCode());

        $syn = $tr[0]['syn'];
        $this->assertInstanceOf(ArrayCollection::class, $translation->getSynonyms());
        /** @var Word $synonym */
        $synonym = $translation->getSynonyms()->first();
        $this->assertSame($syn[0]['text'], $synonym->getText());
        $this->assertSame($syn[0]['pos'], $synonym->getPos());
        $this->assertSame($direction->getLangTo(), $synonym->getLangCode());
    }

    public function testParseReturnValidWordWhenDefEmpty(): void
    {
        $dictionary = $this->getEmptyDictionary();
        $direction = $this->getDirection();

        $word = $this->service->parse(json_encode($dictionary), $direction);

        $this->assertInstanceOf(HandyBag::class, $word);
    }

    private function getEmptyDictionary(): array
    {
        return [
            'head' => [],
            'def' => [],
        ];
    }

    private function getDictionaryWithNounAndAdjective(): array
    {
        return [
            'head' => [],
            'def' => [
                [
                    'text' => 'house',
                    'pos' => 'noun',
                    'ts' => 'haʊs',
                    'tr' => [
                        [
                            'text' => 'дом',
                            'pos' => 'noun',
                            'syn' => [
                                [
                                    'text' => 'здание',
                                    'pos' => 'noun',
                                ],
                                [
                                    'text' => 'домик',
                                    'pos' => 'noun',
                                ],
                                [
                                    'text' => 'жилой дом',
                                    'pos' => 'noun',
                                ],
                                [
                                    'text' => 'домишко',
                                    'pos' => 'noun',
                                ],
                            ],
                            'ex' => [
                                [
                                    'text' => 'auction house',
                                    [
                                        'text' => 'аукционный дом',
                                    ],
                                ],
                                [
                                    'text' => 'father\'s house',
                                    [
                                        'text' => 'отчий дом',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'text' => 'жилье',
                            'pos' => 'noun',
                            'syn' => [
                                [
                                    'text' => 'жилище',
                                    'pos' => 'noun',
                                ],
                            ],
                            'ex' => [
                                [
                                    'text' => 'safe houses',
                                    [
                                        'text' => 'безопасное жилье',
                                    ],
                                ],
                                [
                                    'text' => 'right to housing',
                                    [
                                        'text' => 'право на жилище',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'text' => 'house',
                    'pos' => 'adjective',
                    'ts' => 'haʊs',
                    'tr' => [
                        [
                            'text' => 'домашний',
                            'pos' => 'adjective',
                            'syn' => [
                                [
                                    'text' => 'домовой',
                                    'pos' => 'adjective',
                                ],
                            ],
                            'ex' => [
                                [
                                    'text' => 'house cat',
                                    [
                                        'text' => 'домашняя кошка',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getDictionaryWithAdverbAndUnclear(): array
    {
        return             [
            'head' => [],
            'def' => [
                [
                    'text' => 'at all',
                    'pos' => 'adverb',
                    'ts' => 'ætˈɔːl',
                    'tr' => [
                        [
                            'text' => 'вообще',
                            'pos' => 'adverb',
                            'syn' => [
                                [
                                    'text' => 'совсем',
                                    'pos' => 'adverb',
                                ],
                                [
                                    'text' => 'совершенно',
                                    'pos' => 'adverb',
                                ],
                                [
                                    'text' => 'вовсе',
                                    'pos' => 'adverb',
                                ],
                                [
                                    'text' => 'напрочь',
                                    'pos' => 'adverb',
                                ],
                            ],
                            'mean' => [
                                [
                                    'text' => 'in general',
                                ],
                                [
                                    'text' => 'absolutely',
                                ],
                                [
                                    'text' => 'completely',
                                ],
                            ],
                        ],
                        [
                            'text' => 'ничуть',
                            'pos' => 'adverb',
                            'syn' => [
                                [
                                    'text' => 'нисколько',
                                    'pos' => 'adverb',
                                ],
                                [
                                    'text' => 'нимало',
                                    'pos' => 'adverb',
                                ],
                            ],
                            'mean' => [
                                [
                                    'text' => 'not',
                                ],
                            ],
                        ],
                        [
                            'text' => 'y всех',
                            'pos' => 'adverb',
                        ],
                    ],
                ],
                [
                    'text' => 'at all',
                    'ts' => 'ætˈɔːl',
                    'tr' => [
                        [
                            'text' => ''
                        ]
                    ]
                ]
            ],
        ];
    }

    private function getDirection(): Direction
    {
        $direction = new Direction();
        $direction->setLangFrom('ru');
        $direction->setLangTo('en');
        $direction->setDirection('ru-en');

        return $direction;
    }
}