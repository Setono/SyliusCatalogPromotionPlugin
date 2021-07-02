<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Doctrine\ORM;

use DateTimeInterface;
use Doctrine\DBAL\TransactionIsolationLevel;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @mixin EntityRepository
 */
trait ChannelPricingRepositoryTrait
{
    use HasAnyBeenUpdatedSinceTrait;

    public function resetMultiplier(DateTimeInterface $dateTime, string $bulkIdentifier): int
    {
        \assert($this instanceof EntityRepository);

        $updatedRows = 0;

        $connection = $this->_em->getConnection();
        $oldTransactionIsolation = (int) $connection->getTransactionIsolation();
        $connection->setTransactionIsolation(TransactionIsolationLevel::READ_COMMITTED);

        do {
            $res = 0;
            $connection->beginTransaction();

            try {
                $ids = $this->createQueryBuilder('o')
                    ->select('o.id')
                    ->andWhere('o.multiplier != 1')
                    ->setMaxResults(100)
                    ->getQuery()
                    ->getResult()
                ;

                $res = (int) $this
                    ->createQueryBuilder('o')
                    ->update()
                    ->set('o.multiplier', 1)
                    ->set('o.updatedAt', ':updatedAt')
                    ->set('o.bulkIdentifier', ':bulkIdentifier')
                    ->andWhere('o.id IN (:ids)')
                    ->setParameter('updatedAt', $dateTime)
                    ->setParameter('bulkIdentifier', $bulkIdentifier)
                    ->setParameter('ids', $ids)
                    ->getQuery()
                    ->execute()
                ;

                $connection->commit();

                $updatedRows += $res;
            } catch (\Throwable $e) {
                $connection->rollBack();
            }
        } while ($res > 0);

        $connection->setTransactionIsolation($oldTransactionIsolation);

        return $updatedRows;
    }

    public function updateMultiplier(
        float $multiplier,
        array $productVariantIds,
        array $channelCodes,
        DateTimeInterface $dateTime,
        string $bulkIdentifier,
        bool $exclusive = false,
        bool $manuallyDiscountedProductsExcluded = true
    ): int {
        \assert($this instanceof EntityRepository);

        if (count($channelCodes) === 0 || count($productVariantIds) === 0) {
            return 0;
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

        return (int) $qb->getQuery()->execute();
    }

    public function updatePrices(string $bulkIdentifier): int
    {
        \assert($this instanceof EntityRepository);

        $updatedRows = 0;

        $connection = $this->_em->getConnection();
        $oldTransactionIsolation = (int) $connection->getTransactionIsolation();
        $connection->setTransactionIsolation(TransactionIsolationLevel::READ_COMMITTED);

        do {
            $res = 0;
            $connection->beginTransaction();

            try {
                // get an array of ids to work on
                $ids = $this->createQueryBuilder('o')
                    ->select('o.id')
                    ->andWhere('o.bulkIdentifier = :bulkIdentifier')
                    ->setParameter('bulkIdentifier', $bulkIdentifier)
                    ->setMaxResults(100)
                    ->getQuery()
                    ->getResult();

                // this query handles the case where an original price is set
                // i.e. we have made discounts on this product before
                $updatedRows += (int) $this->createQueryBuilder('o')
                    ->update()
                    ->set('o.price', 'ROUND(o.originalPrice * o.multiplier)')
                    ->andWhere('o.originalPrice is not null')
                    ->andWhere('o.id in (:ids)')
                    ->setParameter('ids', $ids)
                    ->getQuery()
                    ->execute();

                // this query handles the case where a discount hasn't been applied before
                // so we want to move the current price to the original price before changing the price
                $updatedRows += (int) $this->createQueryBuilder('o')
                    ->update()
                    ->set('o.originalPrice', 'o.price')
                    ->set('o.price', 'ROUND(o.price * o.multiplier)')
                    ->andWhere('o.originalPrice is null')
                    ->andWhere('o.multiplier != 1')
                    ->andWhere('o.id in (:ids)')
                    ->setParameter('ids', $ids)
                    ->getQuery()
                    ->execute();

                // this query sets the original price to null where the original price equals the price
                $updatedRows += (int) $this->createQueryBuilder('o')
                    ->update()
                    ->set('o.originalPrice', ':originalPrice')
                    ->andWhere('o.price = o.originalPrice')
                    ->andWhere('o.id in (:ids)')
                    ->setParameter('ids', $ids)
                    ->setParameter('originalPrice', null)
                    ->getQuery()
                    ->execute();

                // set the bulk identifier to null to ensure the loop will come to an end ;)
                $res = (int) $this
                    ->createQueryBuilder('o')
                    ->update()
                    ->set('o.bulkIdentifier', ':null')
                    ->andWhere('o.id IN (:ids)')
                    ->setParameter('null', null)
                    ->setParameter('ids', $ids)
                    ->getQuery()
                    ->execute();

                $connection->commit();

                $updatedRows += $res;
            } catch (\Throwable $e) {
                $connection->rollBack();
            }
        } while ($res > 0);

        $connection->setTransactionIsolation($oldTransactionIsolation);

        return $updatedRows;
    }
}
