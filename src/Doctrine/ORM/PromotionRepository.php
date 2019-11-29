<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionsPlugin\Doctrine\ORM;

use DateTime;
use Setono\SyliusCatalogPromotionsPlugin\Repository\PromotionRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class PromotionRepository extends EntityRepository implements PromotionRepositoryInterface
{
    use HasAnyBeenUpdatedSinceTrait;

    public function findForProcessing(): array
    {
        $dt = new DateTime();

        return $this->createQueryBuilder('o')
            ->andWhere('o.enabled = true')
            ->andWhere('SIZE(o.channels) > 0')
            ->andWhere('o.startsAt is null OR o.startsAt >= :date')
            ->andWhere('o.endsAt is null OR o.endsAt <= :date')
            ->addOrderBy('o.exclusive', 'ASC')
            ->addOrderBy('o.priority', 'ASC')
            ->setParameter('date', $dt)
            ->getQuery()
            ->getResult()
        ;
    }
}
