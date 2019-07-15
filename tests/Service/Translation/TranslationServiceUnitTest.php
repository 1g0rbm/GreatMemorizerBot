<?php

namespace Ig0rbm\Memo\Tests\Service\Translation;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Service\Translation\DirectionParser;
use Ig0rbm\Memo\Service\Translation\TranslationService;
use Ig0rbm\Memo\Service\Translation\ApiWordTranslationInterface;
use Ig0rbm\HandyBag\HandyBag;

class TranslationServiceUnitTest extends TestCase
{
    /** @var TranslationService */
    private $service;

    /** @var ApiWordTranslationInterface|MockObject */
    private $apiTranslation;

    /** @var DirectionParser|MockObject */
    private $directionParser;

    public function setUp(): void
    {
        parent::setUp();
        $this->apiTranslation = $this->getMockBuilder(ApiWordTranslationInterface::class)->getMock();
        $this->directionParser = $this->createMock(DirectionParser::class);

        $this->service = new TranslationService($this->apiTranslation, $this->directionParser);
    }

    public function test(): void
    {
        $this->assertInstanceOf(HandyBag::class, $this->service->translate('ru-en', 'test'));
    }
}