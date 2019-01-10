<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkSpecialsPlugin\Behat\Behaviour;

use Setono\SyliusBulkSpecialsPlugin\Model\Special;
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
        Assert::oneOf($actionType, [Special::ACTION_TYPE_OFF, Special::ACTION_TYPE_INCREASE]);
        $this->getDocument()->fillField('Action type', $actionType);
    }
}
