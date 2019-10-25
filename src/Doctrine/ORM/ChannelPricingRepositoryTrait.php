<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

trait ChannelPricingRepositoryTrait
{
    /**
     * @return QueryBuilder
     */
    abstract public function createQueryBuilder($alias, $indexBy = null);

    public function resetMultiplier(): void
    {
        $this
            ->createQueryBuilder('o')
            ->update()
            ->set('o.multiplier', 1)
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
        bool $exclusive = false
    ): void {
        if (count($channelCodes) === 0) {
            return;
        }

        $qb = $this->createQueryBuilder('channelPricing');

        // This copies parameters from the product variant query builder so we can use them
        // when we do the sub query later
        $qb->setParameters($productVariantQueryBuilder->getParameters());
        $qb->update()
            ->andWhere(sprintf('channelPricing.productVariant IN (%s)', $productVariantQueryBuilder->getDQL()))
            ->andWhere('channelPricing.channelCode IN (:channelCodes)')
            ->setParameter('channelCodes', $channelCodes)
        ;

        if ($exclusive) {
            $qb->set('channelPricing.multiplier', ':multiplier');
        } else {
            $qb->set('channelPricing.multiplier', 'channelPricing.multiplier * :multiplier');
        }

        $qb->setParameter('multiplier', $multiplier);

        $qb->getQuery()->execute();
    }

    public function updatePrices(): void
    {
        $this->createQueryBuilder('channelPricing')
            ->update()
            ->set('channelPricing.price', 'ROUND(channelPricing.originalPrice * channelPricing.multiplier)')
            ->andWhere('channelPricing.originalPrice is not null')
            ->getQuery()
            ->execute()
        ;

        $this->createQueryBuilder('channelPricing')
            ->update()
            ->set('channelPricing.originalPrice', 'channelPricing.price')
            ->set('channelPricing.price', 'ROUND(channelPricing.price * channelPricing.multiplier)')
            ->andWhere('channelPricing.originalPrice is null')
            ->andWhere('channelPricing.multiplier > 1 OR channelPricing.multiplier < 1')
            ->getQuery()
            ->execute()
        ;

        $this->createQueryBuilder('channelPricing')
            ->update()
            ->set('channelPricing.originalPrice', ':originalPrice')
            ->andWhere('channelPricing.price = channelPricing.originalPrice')
            ->setParameter('originalPrice', null)
            ->getQuery()
            ->execute()
        ;
    }
}
