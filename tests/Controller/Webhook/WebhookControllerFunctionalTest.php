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

    public function testWebhookTelegramBot(): void
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
        var_dump($response->getContent());

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    private function getTestBotRequest(): string
    {
        return json_encode([
            'update_id' => $this->faker->unique()->randomNumber(8),
            'message' => [
                'message_id' => $this->faker->unique()->randomNumber(4),
                'from' => [
                    'id' => $this->faker->unique()->randomNumber(9),
                    'is_bot' => false,
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'username' => $this->faker->userName,
                    'language_code' => $this->faker->languageCode
                ],
                'chat' => [
                    'id' => $this->faker->unique()->randomNumber(9),
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'username' => $this->faker->userName,
                    'type' => 'private'
                ],
                'date' => $this->faker->dateTime->getTimestamp(),
                'text' => $this->faker->text(100)
            ]
        ]);
    }
}