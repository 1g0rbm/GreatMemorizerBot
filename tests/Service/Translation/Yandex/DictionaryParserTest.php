<?php

namespace Ig0rbm\Memo\Tests\Service\Yandex;

use Doctrine\Common\Collections\ArrayCollection;
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

    public function testParseReturnWord(): void
    {
        $dictionary = $this->getDictionary();
        $word = $this->service->parse(json_encode($dictionary));

        $this->assertInstanceOf(Word::class, $word);
        $this->assertSame($dictionary['def'][0]['text'], $word->getText());
        $this->assertSame($dictionary['def'][0]['pos'], $word->getPos());
        $this->assertSame($dictionary['def'][0]['ts'], $word->getTranscription());

        $tr = $dictionary['def'][0]['tr'];
        $this->assertInstanceOf(ArrayCollection::class, $word->getTranslations());
        /** @var Word $translation */
        $translation = $word->getTranslations()->first();
        $this->assertSame($tr[1]['text'], $translation->getText());
        $this->assertSame($tr[1]['pos'], $translation->getPos());

        $syn = $tr[1]['syn'];
        $this->assertInstanceOf(ArrayCollection::class, $word->getTranslations());
        /** @var Word $synonym */
        $synonym = $translation->getSynonyms()->first();
        $this->assertSame($syn[4]['text'], $synonym->getText());
        $this->assertSame($syn[4]['pos'], $synonym->getPos());
    }

    private function getDictionary(): array
    {
        return [
            'head' => [],
            'def' => [
                [
                    'text' => 'translate',
                    'pos' => 'verb',
                    'ts' => 'trænsˈleɪt',
                    'tr' => [
                        [
                            'text' => 'переводить',
                            'pos' => 'verb',
                            'syn' => [
                                [
                                    'text' => 'транслировать',
                                    'pos' => 'verb'
                                ],
                                [
                                    'text' => 'преобразовывать',
                                    'pos' => 'verb',
                                    'asp' => 'несов'
                                ],
                                [
                                    'text' => 'перевести',
                                    'pos' => 'verb',
                                    'asp' => 'сов'
                                ],
                                [
                                    'text' => 'преобразовать',
                                    'pos' => 'verb',
                                ],
                                [
                                    'text' => 'преобразовать',
                                    'pos' => 'verb',
                                ],
                            ],
                            'ex' => [
                                [
                                    'text' => 'translate into russian',
                                    [
                                        'text' => 'переводить на русский'
                                    ],
                                ],
                                [
                                    'text' => 'translated text',
                                    [
                                        'text' => 'переведенный текст'
                                    ],
                                ]
                            ]
                        ],
                        [
                            'text' => 'превращать',
                            'pos' => 'verb',
                            'syn' => [
                                [
                                    'text' => 'трансформировать',
                                    'pos' => 'verb'
                                ],
                                [
                                    'text' => 'преобразовывать',
                                    'pos' => 'verb',
                                    'asp' => 'несов'
                                ],
                                [
                                    'text' => 'перевести',
                                    'pos' => 'verb',
                                    'asp' => 'сов'
                                ],
                                [
                                    'text' => 'преобразовать',
                                    'pos' => 'verb',
                                ],
                                [
                                    'text' => 'преобразовать',
                                    'pos' => 'verb',
                                ],
                            ],
                            'ex' => [
                                [
                                    'text' => 'translate into russian',
                                    [
                                        'text' => 'переводить на русский'
                                    ],
                                ],
                                [
                                    'text' => 'translated text',
                                    [
                                        'text' => 'переведенный текст'
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}