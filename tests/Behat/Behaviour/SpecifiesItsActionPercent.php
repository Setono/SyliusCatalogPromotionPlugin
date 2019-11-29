<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionsPlugin\Behat\Behaviour;

use Sylius\Behat\Behaviour\DocumentAccessor;

trait SpecifiesItsActionPercent
{
    use DocumentAccessor;

    /**
     * @param string $actionPercent
     */
    public function specifyActionPercent($actionPercent)
    {
        $this->getDocument()->fillField('Action percent', $actionPercent);
    }
}
