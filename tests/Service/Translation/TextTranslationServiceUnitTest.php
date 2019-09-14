<?php

namespace Ig0rbm\Memo\Tests\Service\Translation;

use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Entity\Translation\Text;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Service\Translation\ApiTextTranslationInterface;
use Ig0rbm\Memo\Service\Translation\TextTranslationService;

class TextTranslationServiceUnitTest extends TestCase
{
    /** @var TextTranslationService */
    private $service;

    /** @var ApiTextTranslationInterface|MockObject */
    private $apiTextTranslation;

    public function setUp(): void
    {
        parent::setUp();

        $this->apiTextTranslation = $this->getMockBuilder(ApiTextTranslationInterface::class)->getMock();

        $this->service = new TextTranslationService($this->apiTextTranslation);
    }

    public function testTranslateReturnFullText(): void
    {
        $text = 'text';
        $direction = $this->getDirection();
        $expectedText = new Text();
        $expectedText->setText($text);
        $expectedText->setLangCode($direction->getLangFrom());

        $this->apiTextTranslation->expects($this->once())
            ->method('getTranslate')
            ->with($direction, $text)
            ->willReturn($expectedText);

        $returnText = $this->service->translate($direction, $text);

        $this->assertSame($expectedText, $returnText);
    }

    private function getDirection(): Direction
    {
        $direction = new Direction();
        $direction->setLangFrom('en');
        $direction->setLangTo('ru');

        return $direction;
    }
}
