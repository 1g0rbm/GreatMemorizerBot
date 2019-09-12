<?php

namespace Ig0rbm\Memo\Tests\Service\Telegram\InlineKeyboard;

use Doctrine\Common\Collections\ArrayCollection;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineKeyboard;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Serializer;
use PHPUnit\Framework\TestCase;

class SerializerUnitTest extends TestCase
{
    /** @var Serializer */
    private $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new Serializer();
    }

    public function testSerializeReturnEmptyArrayIfPassKeyboardWithoutLines(): void
    {
        $this->assertEmpty($this->service->serialize($this->createEmptyKeyboard()));
    }

    public function testSerializeReturnSerializedArray(): void
    {
        $this->assertEquals($this->getExpectedArray(), $this->service->serialize($this->createKeyboard()));
    }

    private function getExpectedArray(): array
    {
        $arr = [
            [
                ['text' => 'Button 1', 'callback_data' => 'data_1'],
                ['text' => 'Button 2', 'callback_data' => 'data_2'],
                ['text' => 'Button 3', 'callback_data' => 'data_3'],
            ],
            [
                ['text' => 'Button 4', 'callback_data' => 'data_4'],
                ['text' => 'Button 5', 'callback_data' => 'data_5'],
                ['text' => 'Button 6', 'callback_data' => 'data_6'],
            ],
        ];

        return $arr;
    }

    private function createKeyboard(): InlineKeyboard
    {
        $keyboard = new InlineKeyboard();
        $keyboard->getButtonsLines()->add(
            new ArrayCollection(
                [
                    new InlineButton('Button 1', 'data_1'),
                    new InlineButton('Button 2', 'data_2'),
                    new InlineButton('Button 3', 'data_3'),
                ]
            )
        );
        $keyboard->getButtonsLines()->add(
            new ArrayCollection(
                [
                    new InlineButton('Button 4', 'data_4'),
                    new InlineButton('Button 5', 'data_5'),
                    new InlineButton('Button 6', 'data_6'),
                ]
            )
        );

        return $keyboard;
    }

    private function createEmptyKeyboard(): InlineKeyboard
    {
        $keyboard = new InlineKeyboard();

        return $keyboard;
    }
}
