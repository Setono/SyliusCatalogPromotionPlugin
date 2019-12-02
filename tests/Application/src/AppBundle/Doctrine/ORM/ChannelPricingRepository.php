<?php

declare(strict_types=1);

namespace AppBundle\Doctrine\ORM;

use Setono\SyliusCatalogPromotionPlugin\Doctrine\ORM\ChannelPricingRepositoryTrait;
use Setono\SyliusCatalogPromotionPlugin\Repository\ChannelPricingRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class ChannelPricingRepository extends EntityRepository implements ChannelPricingRepositoryInterface
{
    use ChannelPricingRepositoryTrait;
}
