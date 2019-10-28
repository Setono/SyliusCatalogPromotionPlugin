<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM;

use DateTimeInterface;
use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

trait ChannelPricingRepositoryTrait
{
    use HasAnyBeenUpdatedSinceTrait;

    /**
     * @return QueryBuilder
     */
    abstract public function createQueryBuilder($alias, $indexBy = null);

    public function resetMultiplier(DateTimeInterface $dateTime): void
    {
        $this
            ->createQueryBuilder('o')
            ->update()
            ->set('o.multiplier', 1)
            ->set('o.updatedAt', ':updatedAt')
            ->andWhere('o.multiplier != 1')
            ->setParameter('updatedAt', $dateTime)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @throws StringsException
     */
    public function updateMultiplier(
        float $multiplier,
        QueryBuilder $productVariantQueryBuilder,
        array $channelCodes,
        DateTimeInterface $dateTime,
        bool $exclusive = false
    ): void {
        if (count($channelCodes) === 0) {
            return;
        }

        // if the same association were added multiple times this will remove any duplicates of product variants
        $productVariantQueryBuilder->distinct();

        $qb = $this->createQueryBuilder('channelPricing');

        // This copies parameters from the product variant query builder so we can use them
        // when we do the sub query later
        $qb->setParameters($productVariantQueryBuilder->getParameters());
        $qb->update()
            ->andWhere(sprintf('channelPricing.productVariant IN (%s)', $productVariantQueryBuilder->getDQL()))
            ->andWhere('channelPricing.channelCode IN (:channelCodes)')
            ->set('channelPricing.updatedAt', ':date')
            ->setParameter('channelCodes', $channelCodes)
            ->setParameter('date', $dateTime)
        ;

        if ($exclusive) {
            $qb->set('channelPricing.multiplier', ':multiplier');
        } else {
            $qb->set('channelPricing.multiplier', 'channelPricing.multiplier * :multiplier');
        }

        $qb->setParameter('multiplier', $multiplier);

        $qb->getQuery()->execute();
    }

    public function updatePrices(DateTimeInterface $dateTime): void
    {
        $this->createQueryBuilder('o')
            ->update()
            ->set('o.price', 'ROUND(o.originalPrice * o.multiplier)')
            ->andWhere('o.originalPrice is not null')
            ->andWhere('o.updatedAt >= :date')
            ->setParameter('date', $dateTime)
            ->getQuery()
            ->execute()
        ;

        $this->createQueryBuilder('o')
            ->update()
            ->set('o.originalPrice', 'o.price')
            ->set('o.price', 'ROUND(o.price * o.multiplier)')
            ->andWhere('o.originalPrice is null')
            ->andWhere('o.multiplier != 1')
            ->andWhere('o.updatedAt >= :date')
            ->setParameter('date', $dateTime)
            ->getQuery()
            ->execute()
        ;

        $this->createQueryBuilder('o')
            ->update()
            ->set('o.originalPrice', ':originalPrice')
            ->andWhere('o.price = o.originalPrice')
            ->andWhere('o.updatedAt >= :date')
            ->setParameter('originalPrice', null)
            ->setParameter('date', $dateTime)
            ->getQuery()
            ->execute()
        ;
    }
}
