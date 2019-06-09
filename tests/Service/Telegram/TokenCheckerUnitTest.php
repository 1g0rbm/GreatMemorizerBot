<?php

namespace Ig0rbm\Memo\Tests\Service\Telegram;

use Faker\Factory;
use Ig0rbm\Memo\Service\Telegram\TokenChecker;
use PHPUnit\Framework\TestCase;
use Faker\Generator;

/**
 * @package Ig0rbm\Memo\Tests\Service\Telegram
 * @group unit
 */
class TokenCheckerUnitTest extends TestCase
{
    /** @var TokenChecker */
    private $tokenChecker;

    /** @var Generator */
    private $faker;

    /** @var string */
    private $token;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->token = $this->faker->sha1;

        $this->tokenChecker = new TokenChecker($this->token);
    }

    public function testIsValidTokenReturnTrue(): void
    {
        $this->assertTrue($this->tokenChecker->isValidToken($this->token));
    }

    public function testIsValidTokenReturnFalse(): void
    {
        $this->assertFalse($this->tokenChecker->isValidToken($this->faker->linuxPlatformToken));
    }
}