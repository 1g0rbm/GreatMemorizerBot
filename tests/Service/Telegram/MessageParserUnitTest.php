<?php

namespace Ig0rbm\Memo\Tests\Service\Telegram;

use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\Text;
use Ig0rbm\Memo\Repository\Telegram\Message\ChatRepository;
use Ig0rbm\Memo\Service\Telegram\TextParser;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Telegram\Message\From;
use Ig0rbm\Memo\Service\Telegram\MessageParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Faker\Factory;
use Faker\Generator;
use ReflectionMethod;

class MessageParserUnitTest extends TestCase
{
    /** @var MessageParser */
    private $service;

    /** @var MockObject|ValidatorInterface */
    private $validator;

    /** @var MockObject|TextParser */
    private $textParser;

    /** @var MockObject|ChatRepository */
    private $chatRepository;

    /** @var Generator */
    private $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = $this->getMockBuilder(ValidatorInterface::class)->getMock();
        $this->textParser = $this->createMock(TextParser::class);
        $this->chatRepository = $this->createMock(ChatRepository::class);
        $this->faker = Factory::create();

        $this->service = new MessageParser($this->validator, $this->textParser, $this->chatRepository);
    }

    public function testCreateChatReturnValidChat(): void
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->willReturn([]);

        $method = new ReflectionMethod(MessageParser::class, 'createChat');
        $method->setAccessible(true);

        $rawMessage = $this->getTestBotRequest();
        /** @var Chat $chat */
        $chat = $method->invoke($this->service, $rawMessage['message']['chat']);

        $this->assertInstanceOf(Chat::class, $chat);
        $this->assertSame($rawMessage['message']['chat']['id'], $chat->getId());
        $this->assertSame($rawMessage['message']['chat']['first_name'], $chat->getFirstName());
        $this->assertSame($rawMessage['message']['chat']['last_name'], $chat->getLastName());
        $this->assertSame($rawMessage['message']['chat']['username'], $chat->getUsername());
        $this->assertSame($rawMessage['message']['chat']['type'], $chat->getType());
    }

    public function testCreateFromReturnValidFrom(): void
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->willReturn([]);

        $method = new ReflectionMethod(MessageParser::class, 'createFrom');
        $method->setAccessible(true);

        $rawMessage = $this->getTestBotRequest();
        /** @var From $from */
        $from = $method->invoke($this->service, $rawMessage['message']['from']);

        $this->assertInstanceOf(From::class, $from);
        $this->assertSame($rawMessage['message']['from']['id'], $from->getId());
        $this->assertSame($rawMessage['message']['from']['first_name'], $from->getFirstName());
        $this->assertSame($rawMessage['message']['from']['last_name'], $from->getLastName());
        $this->assertSame($rawMessage['message']['from']['username'], $from->getUsername());
        $this->assertSame($rawMessage['message']['from']['language_code'], $from->getLanguageCode());
        $this->assertSame($rawMessage['message']['from']['is_bot'], $from->isBot());
    }

    public function testCreateMessageReturnValidMessage(): void
    {
        $this->validator->expects($this->any())
            ->method('validate')
            ->willReturn([]);

        $rawMessage = $this->getTestBotRequest();
        $message = $this->service->createMessage(json_encode($rawMessage));

        $this->assertSame($rawMessage['message']['message_id'], $message->getMessageId());
        $this->assertSame($rawMessage['message']['date'], $message->getDate());
        $this->assertInstanceOf(Text::class, $message->getText());
        $this->assertInstanceOf(From::class, $message->getFrom());
        $this->assertInstanceOf(Chat::class, $message->getChat());
        $this->assertNull($message->getReply());
    }

    public function testCreateMessageWithReplyReturnMessage(): void
    {
        $this->validator->expects($this->any())
            ->method('validate')
            ->willReturn([]);

        $rawMessage = $this->getTestBotReplyRequest();
        $message = $this->service->createMessage(json_encode($rawMessage));

        $this->assertSame($rawMessage['message']['message_id'], $message->getMessageId());
        $this->assertSame($rawMessage['message']['date'], $message->getDate());
        $this->assertInstanceOf(Text::class, $message->getText());
        $this->assertInstanceOf(From::class, $message->getFrom());
        $this->assertInstanceOf(Chat::class, $message->getChat());
        $this->assertInstanceOf(MessageFrom::class, $message->getReply());
    }

    private function getTestBotRequest(): array
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $username = $this->faker->userName;

        return [
            'update_id' => $this->faker->unique()->randomNumber(8),
            'message' => [
                'message_id' => $this->faker->unique()->randomNumber(4),
                'from' => [
                    'id' => $this->faker->unique()->randomNumber(9),
                    'is_bot' => false,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'language_code' => $this->faker->languageCode,
                ],
                'chat' => [
                    'id' => $this->faker->unique()->randomNumber(9),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'type' => 'private',
                ],
                'date' => $this->faker->dateTime->getTimestamp(),
                'text' => $this->faker->text(100),
            ],
        ];
    }

    private function getTestBotReplyRequest(): array
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $username = $this->faker->userName;

        return [
            'update_id' => $this->faker->unique()->randomNumber(8),
            'message' => [
                'message_id' => $this->faker->unique()->randomNumber(4),
                'from' => [
                    'id' => $this->faker->unique()->randomNumber(9),
                    'is_bot' => false,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'language_code' => $this->faker->languageCode,
                ],
                'chat' => [
                    'id' => $this->faker->unique()->randomNumber(9),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'type' => 'private',
                ],
                'date' => $this->faker->dateTime->getTimestamp(),
                'reply_to_message' => [
                    'message_id' => $this->faker->unique()->randomNumber(4),
                    'from' => [
                        'id' => $this->faker->unique()->randomNumber(9),
                        'is_bot' => false,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'username' => $username,
                        'language_code' => $this->faker->languageCode,
                    ],
                    'chat' => [
                        'id' => $this->faker->unique()->randomNumber(9),
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'username' => $username,
                        'type' => 'private',
                    ],
                    'date' => $this->faker->dateTime->getTimestamp(),
                    'text' => 'translation word: adverb [some tr] переводимое слово'
                ],
                'text' => '/save',
            ],
        ];
    }
}
