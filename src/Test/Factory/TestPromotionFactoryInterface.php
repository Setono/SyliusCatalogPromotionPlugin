<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Test\Factory;

use Setono\SyliusCatalogPromotionPlugin\Model\PromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;

interface TestPromotionFactoryInterface
{
    public function create(string $name): PromotionInterface;

    public function createForChannel(string $name, ChannelInterface $channel): PromotionInterface;
}
