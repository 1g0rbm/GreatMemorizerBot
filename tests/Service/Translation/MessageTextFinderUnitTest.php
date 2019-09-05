<?php

namespace Ig0rbm\Memo\Tests\Service\Translation;

use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\Text;
use Ig0rbm\Memo\Service\Translation\MessageTextFinder;
use Ig0rbm\Memo\Service\Translation\TranslationTextParser;
use PHPUnit\Framework\TestCase;

class MessageTextFinderUnitTest extends TestCase
{
    /** @var MessageTextFinder */
    private $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new MessageTextFinder(new TranslationTextParser());
    }

    public function testFindTextReturnTextIfThereIsNotReply(): void
    {
        $message = $this->getMessage();

        $text = $this->service->find($message);

        $this->assertSame($message->getText()->getText(), $text);
    }

    public function testFindTextReturnReplyTextIfThereIsReply(): void
    {
        $msg = 'reply';
        $reply = $this->getReplyMessage($msg);

        $this->assertSame('reply', $this->service->find($reply));
    }

    private function getMessage(): MessageFrom
    {
        $message = new MessageFrom();
        $text = new Text();
        $text->setText('text');
        $message->setText($text);

        return $message;
    }

    private function getReplyMessage(string $msg): MessageFrom
    {
        $translation = sprintf("%s: noun [some tr] \n переводы", $msg);

        $message = new MessageFrom();
        $text = new Text();
        $text->setText('text');
        $message->setText($text);

        $reply = new MessageFrom();
        $replyText = new Text();
        $replyText->setText($translation);
        $reply->setText($replyText);

        $message->setReply($reply);

        return $message;
    }
}
