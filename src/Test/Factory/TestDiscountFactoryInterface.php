<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Test\Factory;

use Setono\SyliusBulkDiscountPlugin\Model\DiscountInterface;
use Sylius\Component\Core\Model\ChannelInterface;

interface TestDiscountFactoryInterface
{
    public function create(string $name): DiscountInterface;

    public function createForChannel(string $name, ChannelInterface $channel): DiscountInterface;
}
