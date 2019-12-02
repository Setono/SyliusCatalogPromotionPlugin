<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionPlugin\Behat\Behaviour;

use Setono\SyliusCatalogPromotionPlugin\Model\Promotion;
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
        Assert::oneOf($actionType, [Promotion::ACTION_TYPE_OFF, Promotion::ACTION_TYPE_INCREASE]);
        $this->getDocument()->fillField('Action type', $actionType);
    }
}
