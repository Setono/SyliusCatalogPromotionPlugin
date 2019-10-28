<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkDiscountPlugin\Behat\Behaviour;

use Setono\SyliusBulkDiscountPlugin\Model\Discount;
use Sylius\Behat\Behaviour\DocumentAccessor;
use Webmozart\Assert\Assert;

trait SpecifiesItsActionType
{
    use DocumentAccessor;

    /**
     * @param string $actionType
     */
    public function specifyActionType($actionType)
    {
        Assert::oneOf($actionType, [Discount::ACTION_TYPE_OFF, Discount::ACTION_TYPE_INCREASE]);
        $this->getDocument()->fillField('Action type', $actionType);
    }
}
