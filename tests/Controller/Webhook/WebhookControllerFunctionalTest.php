<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Controller\Webhook;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;
use Faker\Generator;

/**
 * @group functional
 */
class WebhookControllerFunctionalTest extends WebTestCase
{
    private const CHAT_ID = 233575306;

    private string $secret;

    private Generator $faker;

    public function setUp()
    {
        parent::setUp();
        $this->secret = getenv('TELEGRAM_SECRET');
        $this->faker = Factory::create();
    }

    public function testWebhookTelegramBotReturnStatus401(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('/webhook/bot/memo/%s', 'wrong_secret'),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            $this->getTestBotRequest()
        );

        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testWebhookTelegramBotReturnStatus200(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('/webhook/bot/memo/%s', $this->secret),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            $this->getTestBotRequest()
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('ok', $content);
        $this->assertTrue($content['ok']);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testWebhookTelegramBotWithoutPersonalDataReturnStatus200(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('/webhook/bot/memo/%s', $this->secret),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            $this->getTestBotRequestWithoutPersonalData()
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('ok', $content);
        $this->assertTrue($content['ok']);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testWebhookTelegramBotCallbackQuery(): void
    {
        $this->markTestSkipped('Must be refactored');

        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('/webhook/bot/memo/%s', $this->secret),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            $this->getTestCallbackBotRequest()
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('ok', $content);
        $this->assertTrue($content['ok']);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    private function getTestBotRequestWithoutPersonalData(): string
    {
        return json_encode(
            [
                'update_id' => $this->faker->unique()->randomNumber(8),
                'message' => [
                    'message_id' => $this->faker->unique()->randomNumber(4),
                    'from' => [
                        'id' => $this->faker->unique()->randomNumber(9),
                        'is_bot' => false,
                        'first_name' => null,
                        'last_name' => null,
                        'username' => null,
                        'language_code' => $this->faker->languageCode,
                    ],
                    'chat' => [
                        'id' => self::CHAT_ID,
                        'first_name' => null,
                        'last_name' => null,
                        'username' => null,
                        'type' => 'private',
                    ],
                    'date' => $this->faker->dateTime->getTimestamp(),
                    'text' => '/hello',
                ],
            ]
        );
    }

    private function getTestBotRequest(): string
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $username = $this->faker->userName;

        return json_encode(
            [
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
                        'id' => self::CHAT_ID,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'username' => $username,
                        'type' => 'private',
                    ],
                    'date' => $this->faker->dateTime->getTimestamp(),
                    'text' => '/hello',
                ],
            ]
        );
    }

    private function getTestCallbackBotRequest(): string
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $username = $this->faker->userName;

        return json_encode(
            [
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
                            'id' => self::CHAT_ID,
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'username' => $username,
                            'type' => 'private',
                        ],
                        'date' => $this->faker->dateTime->getTimestamp(),
                        'text' => 'Press button to delete word',
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
                    'data' => 'word1',
                ],
            ]
        );
    }
}
