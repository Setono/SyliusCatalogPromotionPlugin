<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkDiscountPlugin\Behat\Context\Dump;

use Behat\Behat\Context\Context;
use Setono\SyliusBulkDiscountPlugin\Model\DiscountInterface;
use Sylius\Behat\Service\SharedStorage;
use Sylius\Behat\Service\SharedStorageInterface;

final class SpecialContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * CommandsContext constructor.
     * @param SharedStorage $sharedStorage
     */
    public function __construct(
        SharedStorage $sharedStorage
    ) {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Then echo details of that special
     * @Then echo details of special :special
     */
    public function echoSpecialDetails(?DiscountInterface $special = null)
    {
        if (null === $special) {
            $special = $this->sharedStorage->get('special');
        }

        echo sprintf(
            "%s (%s %s%%) channels: %s",
            $special->getName(),
            $special->getActionType(),
            $special->getActionPercent(),
            implode(', ', $special->getChannels()->toArray())
        );
    }

}
