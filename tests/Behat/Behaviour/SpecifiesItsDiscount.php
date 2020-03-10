<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionPlugin\Behat\Behaviour;

use Sylius\Behat\Behaviour\DocumentAccessor;

trait SpecifiesItsDiscount
{
    use DocumentAccessor;

    /**
     * @param string $discount
     */
    public function specifyDiscount($discount)
    {
        $this->getDocument()->fillField('Discount', $discount);
    }
}
