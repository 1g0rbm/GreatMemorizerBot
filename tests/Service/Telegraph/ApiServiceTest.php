<?php

namespace Ig0rbm\Memo\Tests\Service\Telegraph;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Ig0rbm\Memo\Service\Telegraph\ApiService;

/**
 * @group functional
 * @group telegraph
 */
class ApiServiceTest extends WebTestCase
{
    /** @var ApiService */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->service = self::$container->get(ApiService::class);
    }

    public function testGetAccountDefaultInfo(): void
    {
        $response = $this->service->getAccountInfo();

        $this->assertTrue($response['ok']);
        $this->assertEquals('DevMemoBot', $response['result']['short_name']);
        $this->assertEquals('DevGreatMemorizerBot', $response['result']['author_name']);
    }

    public function testGetAccountCustomInfo(): void
    {
        $response = $this->service->getAccountInfo(['short_name']);

        $this->assertTrue($response['ok']);
        $this->assertEquals('DevMemoBot', $response['result']['short_name']);
        $this->assertEquals(1, count($response['result']));
    }
}
