<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Test\Factory;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Sylius\Component\Core\Model\ChannelInterface;

interface TestSpecialFactoryInterface
{
    public function create(string $name): SpecialInterface;

    public function createForChannel(string $name, ChannelInterface $channel): SpecialInterface;
}
