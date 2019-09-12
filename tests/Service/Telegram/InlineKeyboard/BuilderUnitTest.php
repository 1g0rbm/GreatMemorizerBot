<?php

namespace Ig0rbm\Memo\Tests\Service\Telegram\InlineKeyboard;

use Doctrine\Common\Collections\ArrayCollection;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineKeyboard;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\LineButtonsArrayValidator;
use Ig0rbm\Memo\Validator\Constraints\Telegram\InlineKeyboard\InlineButton as AssertInlineButton;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BuilderUnitTest extends TestCase
{
    /** @var Builder */
    private $service;

    /** @var ValidatorInterface|MockObject */
    private $validator;

    public function setUp(): void
    {
        parent::setUp();

        $this->validator = $this->getMockBuilder(ValidatorInterface::class)->getMock();
        $this->service = new Builder(new LineButtonsArrayValidator($this->validator));
    }

    /**
     * @throws ReflectionException
     */
    public function testBuilderCreatedWithEmptyInlineKeyboard(): void
    {
        $inlineKeyboard = $this->getBuilderPrivateKeyboard();
        $this->assertEquals(0, $inlineKeyboard->getButtonsLines()->count());
    }

    /**
     * @throws ReflectionException
     */
    public function testAddLineAddLineToKeyboard(): void
    {
        $btn1text = 'Button 1';
        $btn1CallbackData = 'btn_1';
        $btn2text = 'Button 2';
        $btn2CallbackData = 'btn_2';

        $buttons = [
            new InlineButton($btn1text, $btn1CallbackData),
            new InlineButton($btn2text, $btn2CallbackData),
        ];

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($buttons, $this->getConstraint())
            ->willReturn(new ConstraintViolationList());

        $this->service->addLine($buttons);
        $inlineKeyboard = $this->getBuilderPrivateKeyboard();

        $this->assertEquals(1, $inlineKeyboard->getButtonsLines()->count());

        /** @var ArrayCollection $line */
        $line = $inlineKeyboard->getButtonsLines()->first();
        $this->assertEquals(2, $line->count());

        /** @var InlineButton $button */
        $button1 = $line->first();
        $this->assertEquals($btn1text, $button1->getText());
        $this->assertEquals($btn1CallbackData, $button1->getCallbackData());

        /** @var InlineButton $button */
        $button2 = $line->last();
        $this->assertEquals($btn2text, $button2->getText());
        $this->assertEquals($btn2CallbackData, $button2->getCallbackData());
    }

    /**
     * @throws ReflectionException
     */
    public function testFlushReturnKeyboardAndRecreateKeyboardInsideBuilder(): void
    {
        $btn1text = 'Button 1';
        $btn1CallbackData = 'btn_1';
        $btn2text = 'Button 2';
        $btn2CallbackData = 'btn_2';

        $buttons = [
            new InlineButton($btn1text, $btn1CallbackData),
            new InlineButton($btn2text, $btn2CallbackData),
        ];

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($buttons, $this->getConstraint())
            ->willReturn(new ConstraintViolationList());

        $this->service->addLine($buttons);
        $flushedKeyboard = $this->service->flush();

        $this->assertEquals(1, $flushedKeyboard->getButtonsLines()->count());

        /** @var ArrayCollection $line */
        $line = $flushedKeyboard->getButtonsLines()->first();
        $this->assertEquals(2, $line->count());

        /** @var InlineButton $button */
        $button1 = $line->first();
        $this->assertEquals($btn1text, $button1->getText());
        $this->assertEquals($btn1CallbackData, $button1->getCallbackData());

        /** @var InlineButton $button */
        $button2 = $line->last();
        $this->assertEquals($btn2text, $button2->getText());
        $this->assertEquals($btn2CallbackData, $button2->getCallbackData());

        $inlineKeyboard = $this->getBuilderPrivateKeyboard();
        $this->assertEquals(0, $inlineKeyboard->getButtonsLines()->count());
    }

    private function getConstraint(): Assert\All
    {
        return new Assert\All(['constraints' => new AssertInlineButton()]);
    }

    /**
     * @throws ReflectionException
     */
    private function getBuilderPrivateKeyboard(): InlineKeyboard
    {
        $class = new ReflectionClass(Builder::class);
        $privateKeyboard = $class->getProperty('keyboard');
        $privateKeyboard->setAccessible(true);

        return $privateKeyboard->getValue($this->service);
    }
}
