<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class SpecialRepository extends EntityRepository implements SpecialRepositoryInterface
{
    public function findNonExclusiveEnabledWithAtLeastOneChannel(): array
    {
        return $this->enabledWithAtLeastOneChannelQueryBuilder()
            ->andWhere('o.exclusive = false')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findExclusiveEnabledWithAtLeastOneChannelOrderedByPriorityAscending(): array
    {
        return $this->enabledWithAtLeastOneChannelQueryBuilder()
            ->andWhere('o.exclusive = true')
            ->addOrderBy('o.priority', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAccidentallyDisabled(?DateTimeInterface $date = null): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.startsAt IS NULL OR o.startsAt < :date')
            ->andWhere('o.endsAt IS NULL OR o.endsAt > :date')
            ->setParameter('date', $date ?: new DateTime())
            ->andWhere('o.enabled = 0')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAccidentallyEnabled(?DateTimeInterface $date = null): array
    {
        return $this->createQueryBuilder('o')
            ->orWhere('o.startsAt IS NULL OR o.startsAt >= :date')
            ->orWhere('o.endsAt IS NULL OR o.endsAt <= :date')
            ->setParameter('date', $date ?: new DateTime())
            ->andWhere('o.enabled = 1')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return SpecialInterface[]
     */
    public function findAll(): array
    {
        return $this->findBy([], [
            'priority' => 'desc',
        ]);
    }

    /**
     * @return SpecialInterface[]
     *
     * @throws Exception
     */
    public function findActive(): array
    {
        return $this->filterByActive($this->createQueryBuilder('o'))
            ->addOrderBy('o.priority', 'desc')
            ->getQuery()
            ->getResult()
        ;
    }

    protected function enabledWithAtLeastOneChannelQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.enabled = true')
            ->andWhere('SIZE(o.channels) > 0')
        ;
    }

    /**
     * @throws Exception
     */
    protected function filterByActive(QueryBuilder $queryBuilder, ?DateTimeInterface $date = null): QueryBuilder
    {
        return $queryBuilder
            ->andWhere('o.startsAt IS NULL OR o.startsAt < :date')
            ->andWhere('o.endsAt IS NULL OR o.endsAt > :date')
            ->setParameter('date', $date ?: new DateTime())
        ;
    }
}
