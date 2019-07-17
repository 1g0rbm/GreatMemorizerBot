<?php

namespace Ig0rbm\Memo\Tests\Service\Translation;

use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;
use Ig0rbm\Memo\Service\Translation\ApiTextTranslationInterface;
use Ig0rbm\Memo\Service\Translation\Yandex\DictionaryParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Service\Translation\DirectionParser;
use Ig0rbm\Memo\Service\Translation\TranslationService;
use Ig0rbm\Memo\Service\Translation\ApiWordTranslationInterface;

class TranslationServiceUnitTest extends TestCase
{
    /** @var TranslationService */
    private $service;

    /** @var ApiWordTranslationInterface|MockObject */
    private $apiWordTranslation;

    /** @var ApiTextTranslationInterface|MockObject */
    private $apiTextTranslation;

    /** @var DirectionParser|MockObject */
    private $directionParser;

    /** @var MessageBuilder|MockObject */
    private $messageBuilder;

    public function setUp(): void
    {
        parent::setUp();
        $this->apiWordTranslation = $this->getMockBuilder(ApiWordTranslationInterface::class)->getMock();
        $this->apiTextTranslation = $this->getMockBuilder(ApiTextTranslationInterface::class)->getMock();
        $this->directionParser = $this->createMock(DirectionParser::class);
        $this->messageBuilder = $this->createMock(MessageBuilder::class);

        $this->service = new TranslationService(
            $this->apiWordTranslation,
            $this->apiTextTranslation,
            $this->directionParser,
            $this->messageBuilder
        );
    }

    public function testReturnValidTranslateForWord(): void
    {
        $wordForTranslate = 'test';
        $direction = $this->getDirection();

        $this->directionParser->expects($this->once())
            ->method('parse')
            ->willReturn($direction);

        $wordCollection = $this->getWordsCollection($direction);
        $this->apiWordTranslation->expects($this->once())
            ->method('getTranslate')
            ->with($direction, $wordForTranslate)
            ->willReturn($wordCollection);

        $this->messageBuilder->expects($this->once())
            ->method('buildFromWords')
            ->with($wordCollection)
            ->willReturn(sprintf('*%s*', $wordForTranslate));

        $this->messageBuilder->expects($this->never())
            ->method('buildFromText');

        $this->assertSame(
            sprintf('*%s*', $wordForTranslate),
            $this->service->translate('en-ru', $wordForTranslate)
        );
    }

    private function getWordsCollection(Direction $direction): HandyBag
    {
        return (new DictionaryParser())->parse(json_encode($this->getDictionary()), $direction);
    }

    private function getDictionary(): array
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

    private function getDirection(): Direction
    {
        $direction = new Direction();
        $direction->setLangTo('ru');
        $direction->setLangFrom('en');
        $direction->setLangTo('en-ru');

        return $direction;
    }
}