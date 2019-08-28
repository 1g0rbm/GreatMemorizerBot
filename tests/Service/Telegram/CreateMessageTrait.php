<?php

namespace Ig0rbm\Memo\Tests\Service\Telegram;

use Faker\Factory;
use Faker\Generator;

trait CreateMessageTrait
{
    public function getTestBotRequest(): array
    {
        $faker = $this->getFaker();

        $firstName = $faker->firstName;
        $lastName = $faker->lastName;
        $username = $faker->userName;

        return [
            'update_id' => $faker->unique()->randomNumber(8),
            'message' => [
                'message_id' => $faker->unique()->randomNumber(4),
                'from' => [
                    'id' => $faker->unique()->randomNumber(9),
                    'is_bot' => false,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'language_code' => $faker->languageCode,
                ],
                'chat' => [
                    'id' => $faker->unique()->randomNumber(9),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'type' => 'private',
                ],
                'date' => $faker->dateTime->getTimestamp(),
                'text' => $faker->text(100),
            ],
        ];
    }

    private function getFaker(): Generator
    {
        return  Factory::create();
    }
}
