<?php

namespace Ig0rbm\Memo\Tests\Service\Yandex;

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
        $this->assertInstanceOf(Word::class, $this->service->parse($this->getDictionary()));
    }

    private function getDictionary(): string
    {
        return json_encode([
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
        ]);
    }
}