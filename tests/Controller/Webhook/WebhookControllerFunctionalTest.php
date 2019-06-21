<?php

namespace Ig0rbm\Memo\Tests\Controller\Webhook;

use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Faker\Generator;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Ig0rbm\Memo\Tests\Controller\Webhook
 * @group functional
 */
class WebhookControllerFunctionalTest extends WebTestCase
{
    private const CHAT_ID = 233575306;

    /** @var string */
    private $secret;

    /** @var Generator */
    private $faker;

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
                'CONTENT_TYPE' => 'application/json'
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
                'CONTENT_TYPE' => 'application/json'
            ],
            $this->getTestBotRequest()
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('ok', $content);
        $this->assertTrue($content['ok']);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    private function getTestBotRequest(): string
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $username = $this->faker->userName;

        return json_encode([
            'update_id' => $this->faker->unique()->randomNumber(8),
            'message' => [
                'message_id' => $this->faker->unique()->randomNumber(4),
                'from' => [
                    'id' => $this->faker->unique()->randomNumber(9),
                    'is_bot' => false,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'language_code' => $this->faker->languageCode
                ],
                'chat' => [
                    'id' => self::CHAT_ID,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'type' => 'private'
                ],
                'date' => $this->faker->dateTime->getTimestamp(),
                'text' => '/hello'
            ]
        ]);
    }
}