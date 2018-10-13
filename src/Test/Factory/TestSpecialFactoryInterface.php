<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Test\Factory;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Sylius\Component\Core\Model\ChannelInterface;

interface TestSpecialFactoryInterface
{
    /**
     * @param string $name
     *
     * @return SpecialInterface
     */
    public function create(string $name): SpecialInterface;

    /**
     * @param string $name
     * @param ChannelInterface $channel
     *
     * @return SpecialInterface
     */
    public function createForChannel(string $name, ChannelInterface $channel): SpecialInterface;
}
