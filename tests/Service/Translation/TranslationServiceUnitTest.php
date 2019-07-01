<?php

namespace Ig0rbm\Memo\Tests\Service\Translation;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Service\Translation\DirectionParser;
use Ig0rbm\Memo\Service\Translation\TranslationService;
use Ig0rbm\Memo\Service\Translation\ApiTranslationInterface;

class TranslationServiceUnitTest extends TestCase
{
    /** @var TranslationService */
    private $service;

    /** @var ApiTranslationInterface|MockObject */
    private $apiTranslation;

    /** @var DirectionParser|MockObject */
    private $directionParser;

    public function setUp(): void
    {
        parent::setUp();
        $this->apiTranslation = $this->getMockBuilder(ApiTranslationInterface::class)->getMock();
        $this->directionParser = $this->createMock(DirectionParser::class);

        $this->service = new TranslationService($this->apiTranslation, $this->directionParser);
    }

    public function test(): void
    {
        $this->assertInstanceOf(Word::class, $this->service->translate('ru-en', 'test'));
    }
}