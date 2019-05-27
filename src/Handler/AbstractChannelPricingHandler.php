<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Sylius\Component\Core\Model\ChannelPricingInterface;

abstract class AbstractChannelPricingHandler extends AbstractHandler implements ChannelPricingRecalculateHandlerInterface
{
    public function handle($object): void
    {
        if (!$object instanceof ChannelPricingInterface) {
            return;
        }

        $this->handleChannelPricing($object);
    }
}
