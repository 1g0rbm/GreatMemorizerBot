<?php

namespace Ig0rbm\Memo\Tests\Service\Translation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Service\Translation\WordsPersistService;
use Ig0rbm\Memo\Service\Translation\WordTranslationService;
use Ig0rbm\Memo\Repository\Translation\WordRepository;
use Ig0rbm\Memo\Service\Translation\ApiWordTranslationInterface;

class WordTranslationServiceUnitTest extends TestCase
{
    private WordTranslationService $service;

    /** @var ApiWordTranslationInterface|MockObject */
    private $apiWordTranslation;

    /** @var WordRepository|MockObject */
    private $wordRepository;

    /** @var WordsPersistService|MockObject */
    private $wordsPersistService;

    public function setUp(): void
    {
        parent::setUp();

        $this->apiWordTranslation = $this->getMockBuilder(ApiWordTranslationInterface::class)->getMock();
        $this->wordRepository = $this->createMock(WordRepository::class);
        $this->wordsPersistService = $this->createMock(WordsPersistService::class);

        $this->service = new WordTranslationService(
            $this->apiWordTranslation,
            $this->wordRepository,
            $this->wordsPersistService
        );
    }

    /**
     * @throws ORMException
     */
    public function testTranslateRepositoryReturnWordsBag(): void
    {
        $text = 'text';
        $direction = $this->getDirection();
        $bag = $this->getCollection();

        $this->wordRepository->expects($this->once())
            ->method('findWordsCollection')
            ->with($text)
            ->willReturn($bag);

        $result = $this->service->translate($direction, $text);

        $this->assertSame($bag, $result);
    }

    /**
     * @throws ORMException
     */
    public function testTranslationUseApiForTranslate(): void
    {
        $text = 'text';
        $direction = $this->getDirection();
        $direction->setIsSavable(true);
        $bag = $this->getCollection();

        $this->wordRepository->expects($this->once())
            ->method('findWordsCollection')
            ->with($text)
            ->willReturn(null);

        $this->apiWordTranslation->expects($this->once())
            ->method('getTranslate')
            ->with($direction, $text)
            ->willReturn($bag);

        $this->wordsPersistService->expects($this->once())
            ->method('save')
            ->with($bag);

        $result = $this->service->translate($direction, $text);

        $this->assertSame($bag, $result);
    }

    private function getCollection(): Collection
    {
        return new ArrayCollection();
    }

    private function getDirection(): Direction
    {
        $direction = new Direction();
        $direction->setLangFrom('en');
        $direction->setLangTo('ru');
        $direction->setIsSavable(true);

        return $direction;
    }
}
