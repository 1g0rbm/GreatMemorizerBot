<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Telegram;

use Doctrine\ORM\ORMException;
use Faker\Factory;
use Faker\Generator;
use Ig0rbm\Memo\Entity\Telegram\Message\CallbackQuery;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Telegram\Message\From;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\Text;
use Ig0rbm\Memo\Service\InitializeAccountService;
use Ig0rbm\Memo\Service\Telegram\CallbackDataParser;
use Ig0rbm\Memo\Service\Telegram\MessageParser;
use Ig0rbm\Memo\Service\Telegram\TextParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MessageParserUnitTest extends TestCase
{
    private MessageParser $service;

    /** @var MockObject|ValidatorInterface */
    private ValidatorInterface $validator;

    /** @var MockObject|InitializeAccountService */
    private InitializeAccountService $initializeAccountService;

    private Generator $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = $this->getMockBuilder(ValidatorInterface::class)->getMock();
        $this->initializeAccountService = $this->createMock(InitializeAccountService::class);
        $this->faker = Factory::create();

        $this->service = new MessageParser(
            $this->validator,
            new TextParser(new CallbackDataParser()),
            $this->initializeAccountService,
            new CallbackDataParser()
        );
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

    /**
     * @throws ORMException
     */
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

    /**
     * @throws ORMException
     */
    public function testCreateMessageReturnValidMessageWithCallbackQuery(): void
    {
        $this->validator->expects($this->any())
            ->method('validate')
            ->willReturn([]);

        $rawMessage = $this->getTestCallbackBotCommandRequest();
        $message = $this->service->createMessage(json_encode($rawMessage));

        $this->assertSame($rawMessage['callback_query']['message']['message_id'], $message->getMessageId());
        $this->assertSame($rawMessage['callback_query']['message']['date'], $message->getDate());
        $this->assertInstanceOf(Text::class, $message->getText());
        $this->assertInstanceOf(From::class, $message->getFrom());
        $this->assertInstanceOf(Chat::class, $message->getChat());
        $this->assertNull($message->getReply());
        $this->assertSame($rawMessage['callback_query']['data'], $message->getCallbackQuery()->getData()->getCommand());
    }

    /**
     * @throws ORMException
     */
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

    /**
     * @throws ORMException
     */
    public function testCreateCallbackQueryMessage(): void
    {
        $this->validator->expects($this->any())
            ->method('validate')
            ->willReturn([]);

        $rawMessage = $this->getTestCallbackBotRequest();
        $message = $this->service->createMessage(json_encode($rawMessage));

        $this->assertSame($rawMessage['callback_query']['message']['message_id'], $message->getMessageId());
        $this->assertSame($rawMessage['callback_query']['message']['date'], $message->getDate());
        $this->assertInstanceOf(Text::class, $message->getText());
        $this->assertInstanceOf(From::class, $message->getFrom());
        $this->assertInstanceOf(Chat::class, $message->getChat());
        $this->assertInstanceOf(CallbackQuery::class, $message->getCallbackQuery());
        $this->assertSame($rawMessage['callback_query']['data'], $message->getCallbackQuery()->getData()->getText());
        $this->assertNull($message->getCallbackCommand());
    }

    private function getTestCallbackBotCommandRequest(): array
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $username = $this->faker->userName;

        return [
            'update_id' => $this->faker->unique()->randomNumber(8),
            'callback_query' => [
                'id' => $this->faker->unique()->randomNumber(4),
                'from' => [
                    'id' => $this->faker->unique()->randomNumber(9),
                    'is_bot' => false,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'language_code' => $this->faker->languageCode,
                ],
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
                    'text' => 'text text text to text at text',
                    'reply_markup' => [
                        'inline_keyboard' => [
                            [
                                ['text' => 'command', 'callback_data' => '/command'],
                            ],
                        ],
                    ],
                ],
                'chat_instance' => '-5844625820849856935',
                'data' => '/command',
            ]
        ];
    }

    private function getTestCallbackBotRequest(): array
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $username = $this->faker->userName;

        return [
            'update_id' => $this->faker->unique()->randomNumber(8),
            'callback_query' => [
                'id' => $this->faker->unique()->randomNumber(4),
                'from' => [
                    'id' => $this->faker->unique()->randomNumber(9),
                    'is_bot' => false,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'language_code' => $this->faker->languageCode,
                ],
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
                    'text' => '/edit Press button to delete word',
                    'reply_markup' => [
                        'inline_keyboard' => [
                            [
                                ['text' => 'word1', 'callback_data' => 'delete:word1'],
                            ],
                            [
                                ['text' => 'word2', 'callback_data' => 'delete:word2'],
                            ],
                        ],
                    ],
                ],
                'chat_instance' => '-5844625820849856935',
                'data' => 'delete:word1',
            ]
        ];
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
                    'text' => 'translation word: adverb [some tr] переводимое слово',
                ],
                'text' => '/save',
            ],
        ];
    }
}
