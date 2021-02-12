<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Doctrine\ORM;

use DateTimeInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @mixin EntityRepository
 */
trait ChannelPricingRepositoryTrait
{
    use HasAnyBeenUpdatedSinceTrait;

    public function resetMultiplier(DateTimeInterface $dateTime): void
    {
        \assert($this instanceof EntityRepository);

        $this
            ->createQueryBuilder('o')
            ->update()
            ->set('o.multiplier', 1)
            ->set('o.bulkIdentifier', ':null')
            ->set('o.updatedAt', ':updatedAt')
            ->andWhere('o.multiplier != 1')
            ->setParameter('null', null)
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
        string $bulkIdentifier,
        bool $exclusive = false,
        bool $manuallyDiscountedProductsExcluded = true
    ): void {
        \assert($this instanceof EntityRepository);

        if (count($channelCodes) === 0 || count($productVariantIds) === 0) {
            return;
        }

        $qb = $this->createQueryBuilder('channelPricing');

        $qb->update()
            ->andWhere('channelPricing.productVariant IN (:productVariantIds)')
            ->andWhere('channelPricing.channelCode IN (:channelCodes)')
            ->set('channelPricing.updatedAt', ':date')
            ->set('channelPricing.bulkIdentifier', ':bulkIdentifier')
            ->setParameter('productVariantIds', $productVariantIds)
            ->setParameter('channelCodes', $channelCodes)
            ->setParameter('date', $dateTime)
            ->setParameter('bulkIdentifier', $bulkIdentifier)
        ;

        if ($manuallyDiscountedProductsExcluded) {
            $qb->andWhere('channelPricing.manuallyDiscounted = false');
        }

        if ($exclusive) {
            $qb->set('channelPricing.multiplier', ':multiplier');
        } else {
            $qb->set('channelPricing.multiplier', 'channelPricing.multiplier * :multiplier');
        }

        $qb->setParameter('multiplier', $multiplier);

        $qb->getQuery()->execute();
    }

    public function updatePrices(DateTimeInterface $dateTime, string $bulkIdentifier): void
    {
        \assert($this instanceof EntityRepository);

        $this->createQueryBuilder('o')
            ->update()
            ->set('o.price', 'ROUND(o.originalPrice * o.multiplier)')
            ->andWhere('o.originalPrice is not null')
            ->andWhere('o.updatedAt >= :date')
            ->andWhere('o.bulkIdentifier = :bulkIdentifier')
            ->setParameter('date', $dateTime)
            ->setParameter('bulkIdentifier', $bulkIdentifier)
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
            ->andWhere('o.bulkIdentifier = :bulkIdentifier')
            ->setParameter('date', $dateTime)
            ->setParameter('bulkIdentifier', $bulkIdentifier)
            ->getQuery()
            ->execute()
        ;

        $this->createQueryBuilder('o')
            ->update()
            ->set('o.originalPrice', ':originalPrice')
            ->andWhere('o.price = o.originalPrice')
            ->andWhere('o.updatedAt >= :date')
            ->andWhere('o.bulkIdentifier = :bulkIdentifier')
            ->setParameter('originalPrice', null)
            ->setParameter('date', $dateTime)
            ->setParameter('bulkIdentifier', $bulkIdentifier)
            ->getQuery()
            ->execute()
        ;
    }
}
