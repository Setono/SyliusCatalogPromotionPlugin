<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusBulkSpecialsPlugin\Model\Special;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\Product;

/**
 * Class SpecialRepository
 */
class SpecialRepository extends EntityRepository implements SpecialRepositoryInterface
{
    /**
     * Actually, that is not accidentally, just time going on...
     *
     * @return array|Special[]
     */
    public function findAccidentallyDisabled(?\DateTimeInterface $date = null): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.startsAt IS NOT NULL AND o.startsAt < :date')
            ->andWhere('o.endsAt IS NOT NULL AND o.endsAt > :date')
            ->setParameter('date', $date ?: new \DateTime())
            ->andWhere('o.enabled = 0')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Actually, that is not accidentally, just time going on...
     *
     * @return array|Special[]
     */
    public function findAccidentallyEnabled(?\DateTimeInterface $date = null): array
    {
        return $this->createQueryBuilder('o')
            ->orWhere('o.startsAt IS NOT NULL AND o.startsAt >= :date')
            ->orWhere('o.endsAt IS NOT NULL AND o.endsAt <= :date')
            ->setParameter('date', $date ?: new \DateTime())
            ->andWhere('o.enabled = 1')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param Product $product
     *
     * @return array|Special
     */
    public function findByProduct(Product $product): array
    {
        return $this->filterByActive($this->createQueryBuilder('o'))
            ->join('o.rules', 'rule')
            ->andWhere('rule.type')
            ->addOrderBy('o.priority', 'desc')
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @param ChannelInterface $channel
//     *
//     * @return array|Special
//     */
//    public function findActiveByChannel(ChannelInterface $channel): array
//    {
//        return $this->filterByActive($this->createQueryBuilder('o'))
//            ->andWhere(':channel MEMBER OF o.channels')
//            ->setParameter('channel', $channel)
//            ->addOrderBy('o.priority', 'DESC')
//            ->getQuery()
//            ->getResult()
//            ;
//    }

    /**
     * @return array|Special
     */
    public function findAll(): array
    {
        return $this->findBy([], [
            'priority' => 'desc',
        ]);
    }

    /**
     * @return array|Special
     */
    public function findActive(): array
    {
        return $this->filterByActive($this->createQueryBuilder('o'))
            ->addOrderBy('o.priority', 'desc')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param \DateTimeInterface|null $date
     *
     * @return QueryBuilder
     */
    protected function filterByActive(QueryBuilder $queryBuilder, ?\DateTimeInterface $date = null): QueryBuilder
    {
        return $queryBuilder
            ->andWhere('o.startsAt IS NULL OR o.startsAt < :date')
            ->andWhere('o.endsAt IS NULL OR o.endsAt > :date')
            ->setParameter('date', $date ?: new \DateTime())
            ;
    }
}
