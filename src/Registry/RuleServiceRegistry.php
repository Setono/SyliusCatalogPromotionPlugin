<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionsPlugin\Registry;

use Setono\SyliusCatalogPromotionsPlugin\Rule\RuleInterface;
use Sylius\Component\Registry\ServiceRegistry;

class RuleServiceRegistry extends ServiceRegistry
{
    public function __construct()
    {
        parent::__construct(
            RuleInterface::class,
            'rule'
        );
    }
}
