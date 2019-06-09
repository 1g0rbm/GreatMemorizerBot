<?php

namespace Ig0rbm\Memo\Tests\Service\Telegram;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Service\Telegram\MessageParser;

class MessageParserUnitTest extends TestCase
{
    /** @var MessageParser */
    private $service;

    /** @var MockObject|ValidatorInterface */
    private $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = $this->getMockBuilder(ValidatorInterface::class)->getMock();

        $this->service = new MessageParser($this->validator);
    }

    public function testCreateMessage(): void
    {

    }
}
