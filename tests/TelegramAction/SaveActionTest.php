<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Controller\Webhook;

use Faker\Factory;
use Faker\Generator;
use Ig0rbm\Memo\Repository\Telegram\Message\ChatRepository;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Repository\Translation\WordRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use function getenv;
use function json_decode;
use function sprintf;

/**
 * @group functional
 * @grooup controller
 */
class SaveActionTest extends WebTestCase
{
    private const CHAT_ID = 233575306;

    private string $secret;

    private Generator $faker;

    /** @var string[] */
    private array $wordList;

    private WordListRepository $wordListRepository;

    private ChatRepository $chatRepository;

    private WordRepository $wordRepository;

    public function setUp()
    {
        self::bootKernel();

        parent::setUp();
        $this->secret = getenv('TELEGRAM_SECRET');
        $this->faker = Factory::create();

        $this->wordListRepository = static::$container->get(WordListRepository::class);
        $this->wordRepository     = static::$container->get(WordRepository::class);
        $this->chatRepository     = static::$container->get(ChatRepository::class);

        $this->wordList = ['house', 'good', 'god', 'name', 'pain', 'happy', 'love', 'sun', 'nail', 'beach', 'string'];
    }

    public function testWebhookTelegramBotReturnStatus200(): void
    {
        $stringWord = $this->faker->randomElement($this->wordList);

        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('/webhook/bot/memo/%s', $this->secret),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            $this->getTestBotRequest($stringWord)
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('ok', $content);
        $this->assertTrue($content['ok']);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $chat = $this->chatRepository->findChatById(self::CHAT_ID);
        $wordList = $this->wordListRepository->findByChat($chat);
        $word = $this->wordRepository->findOneByText($stringWord);

        $this->assertTrue($wordList->getWords()->contains($word));
    }

    private function getTestBotRequest(string $word): string
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
                    'text' => sprintf('/save %s', $word),
                ],
            ]
        );
    }
}