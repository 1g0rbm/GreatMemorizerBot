<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Quiz;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Repository\Translation\WordRepository;
use Ig0rbm\Memo\Service\Quiz\QuizStepBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group quiz
 */
class QuizStepBuilderUnitTest extends TestCase
{
    private QuizStepBuilder $service;

    /** @var WordRepository|MockObject */
    private WordRepository $wordRepo;

    /** @var WordListRepository|MockObject */
    private WordListRepository $wordListRepo;

    public function setUp(): void
    {
        parent::setUp();

        $this->wordRepo = $this->createMock(WordRepository::class);
        $this->wordListRepo = $this->createMock(WordListRepository::class);

        $this->service = new QuizStepBuilder($this->wordRepo, $this->wordListRepo);
    }

    /**
     * @throws DBALException
     */
    public function testBuildReturnWordCollection(): void
    {
        $quiz        = new Quiz();
        $quiz->setLength(5);

        $this->wordRepo
            ->expects($this->once())
            ->method('getRandomWords')
            ->with('en', 'noun', $quiz->getLength() * 4)
            ->willReturn($this->createWordCollection());

        $collection = $this->service->buildForQuiz($quiz);

        $this->assertInstanceOf(ArrayCollection::class, $collection);
        $this->assertInstanceOf(QuizStep::class, $collection->first());
        $this->assertEquals(5, $collection->count());
    }

    private function createWordCollection(): ArrayCollection
    {
        return new ArrayCollection([
            $this->createWord('home', 'noun'),
            $this->createWord('love', 'noun'),
            $this->createWord('run', 'noun'),
            $this->createWord('health', 'noun'),
            $this->createWord('wind', 'noun'),
            $this->createWord('home', 'noun'),
            $this->createWord('love', 'noun'),
            $this->createWord('run', 'noun'),
            $this->createWord('health', 'noun'),
            $this->createWord('wind', 'noun'),
            $this->createWord('home', 'noun'),
            $this->createWord('love', 'noun'),
            $this->createWord('run', 'noun'),
            $this->createWord('health', 'noun'),
            $this->createWord('wind', 'noun'),
            $this->createWord('home', 'noun'),
            $this->createWord('love', 'noun'),
            $this->createWord('run', 'noun'),
            $this->createWord('health', 'noun'),
            $this->createWord('wind', 'noun'),
        ]);
    }

    private function createWord(string $text, string $pos): Word
    {
        $word = new Word();
        $word->setText($text);
        $word->setPos($pos);

        return $word;
    }
}
