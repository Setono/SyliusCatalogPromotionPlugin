<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Doctrine\ORM;

use DateTimeInterface;
use Doctrine\ORM\QueryBuilder;

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

    public function updateMultiplier(
        float $multiplier,
        array $productVariantIds,
        array $channelCodes,
        DateTimeInterface $dateTime,
        bool $exclusive = false
    ): void {
        if (count($channelCodes) === 0 || count($productVariantIds) === 0) {
            return;
        }

        $qb = $this->createQueryBuilder('channelPricing');

        $qb->update()
            ->andWhere('channelPricing.productVariant IN (:productVariantIds)')
            ->andWhere('channelPricing.channelCode IN (:channelCodes)')
            ->set('channelPricing.updatedAt', ':date')
            ->setParameter('productVariantIds', $productVariantIds)
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
