<?php

namespace Ig0rbm\Memo\Tests\Service\Quiz;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Repository\Translation\WordRepository;
use Ig0rbm\Memo\Service\Quiz\QuizStepBuilder;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Translation\Word;

class QuizStepBuilderUnitTest extends TestCase
{
    /** @var QuizStepBuilder */
    private $service;

    /** @var WordRepository|MockObject */
    private $wordRepo;

    public function setUp(): void
    {
        parent::setUp();

        $this->wordRepo = $this->createMock(WordRepository::class);

        $this->service = new QuizStepBuilder($this->wordRepo);
    }

    /**
     * @throws DBALException
     */
    public function testBuildReturnWordCollection(): void
    {
        $answerCount = 5;
        $quiz        = new Quiz();
        $quiz->setLength(5);

        $this->wordRepo
            ->expects($this->once())
            ->method('getRandomWords')
            ->with('en', 'noun', $quiz->getLength() * $answerCount)
            ->willReturn($this->createWordCollection());

        $collection = $this->service->buildForQuiz($quiz, $answerCount);

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
