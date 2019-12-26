<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Repository\Translation;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Exception\Translation\DirectionException;

class DirectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Direction::class);
    }

    public function findByFromAndTo(string $from, string $to): ?Direction
    {
        /** @var Direction|null $direction */
        $direction = $this->findOneBy(['langFrom' => $from, 'langTo' => $to]);

        return $direction;
    }

    public function getDirectionById(int $directionId): Direction
    {
        /** @var Direction|null $direction */
        $direction = $this->findOneBy(['id' => $directionId]);
        if ($direction === null) {
            throw DirectionException::becauseDirectionNotFound($directionId);
        }

        return $direction;
    }

    public function getDefaultDirection(): Direction
    {
        /** @var Direction $direction */
        $direction = $this->findOneBy(['langFrom' => Direction::LANG_EN, 'langTo' => Direction::LANG_RU]);

        if ($direction === null) {
            throw DirectionException::becauseDefaultDirectionNotFound();
        }

        return $direction;
    }

    /**
     * @throws ORMException
     */
    public function addDirection(Direction $direction): void
    {
        $this->getEntityManager()->persist($direction);
    }
}
