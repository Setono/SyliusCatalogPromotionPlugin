<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Sylius\Component\Core\Model\ChannelPricingInterface;

/**
 * Class AbstractChannelPricingHandler
 */
abstract class AbstractChannelPricingHandler implements ChannelPricingRecalculateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($object): void
    {
        if (!$object instanceof ChannelPricingInterface) {
            return;
        }

        $this->handleChannelPricing($object);
    }
}
