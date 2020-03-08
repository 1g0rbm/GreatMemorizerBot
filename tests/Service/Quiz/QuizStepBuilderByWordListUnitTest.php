<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Quiz;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Exception\Quiz\QuizStepBuilderException;
use Ig0rbm\Memo\Repository\Translation\WordRepository;
use Ig0rbm\Memo\Service\Quiz\QuizStepBuilderByWordList;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group quiz
 */
class QuizStepBuilderByWordListUnitTest extends TestCase
{
    private QuizStepBuilderByWordList $service;

    /** @var WordRepository|MockObject */
    private WordRepository $wordRepo;

    public function setUp(): void
    {
        parent::setUp();

        $this->wordRepo = $this->createMock(WordRepository::class);
        $this->service  = new QuizStepBuilderByWordList($this->wordRepo);
    }

    /**
     * @throws DBALException
     */
    public function testDoThrowExceptionIfThereIsNoWordListId(): void
    {
        $quiz = new Quiz();
        $quiz->setId(1);
        $quiz->setLength(5);

        $this->expectException(QuizStepBuilderException::class);

        $this->service->do($quiz);
    }

    /**
     * @throws DBALException
     */
    public function testDoReturnQuizStepCollection(): void
    {
        $quiz = new Quiz();
        $quiz->setId(1);
        $quiz->setLength(5);
        $quiz->setWordListId(1);

        $rightWords = $this->createRightWordCollection();

        $this->wordRepo->expects($this->once())
            ->method('getRandomWordsByWordListId')
            ->with(
                Direction::LANG_EN,
                ['noun', 'verb', 'adjective', 'adverb'],
                $quiz->getWordListId(),
                $quiz->getLength()
            )
            ->willReturn($rightWords);

        $this->wordRepo->expects($this->once())
            ->method('getRandomWords')
            ->with(
                Direction::LANG_EN,
                ['noun', 'verb', 'adjective', 'adverb'],
                ($quiz->getLength() * QuizStep::DEFAULT_LENGTH) - QuizStep::DEFAULT_LENGTH,
                $rightWords->map(static fn(Word $word) => $word->getId())->toArray()
            )
            ->willReturn($this->createWrongCollection());

        $collection = $this->service->do($quiz);

        $this->assertEquals(4, $collection->count());
    }

    private function createRightWordCollection(): ArrayCollection
    {
        return new ArrayCollection([
            $this->createWord(1, 'home', 'noun'),
            $this->createWord(2, 'love', 'noun'),
            $this->createWord(3, 'run', 'noun'),
            $this->createWord(4, 'health', 'noun'),
        ]);
    }

    private function createWrongCollection(): ArrayCollection
    {
        return new ArrayCollection([
            $this->createWord(5, 'wind', 'noun'),
            $this->createWord(6, 'home', 'noun'),
            $this->createWord(7, 'love', 'noun'),
            $this->createWord(8, 'run', 'noun'),
            $this->createWord(9, 'health', 'noun'),
            $this->createWord(10, 'wind', 'noun'),
            $this->createWord(11, 'home', 'noun'),
            $this->createWord(12, 'love', 'noun'),
            $this->createWord(13, 'run', 'noun'),
            $this->createWord(14, 'health', 'noun'),
            $this->createWord(15, 'wind', 'noun'),
            $this->createWord(16, 'home', 'noun'),
            $this->createWord(17, 'love', 'noun'),
            $this->createWord(18, 'run', 'noun'),
            $this->createWord(19, 'health', 'noun'),
            $this->createWord(20, 'wind', 'noun'),
        ]);
    }

    private function createWord(int $id, string $text, string $pos): Word
    {
        $word = new Word();
        $word->setId($id);
        $word->setText($text);
        $word->setPos($pos);

        return $word;
    }
}
