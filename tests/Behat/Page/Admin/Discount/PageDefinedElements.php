<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionsPlugin\Behat\Page\Admin\Discount;

/**
 * Trait PageDefinedElements
 */
trait PageDefinedElements
{
    protected function getDefinedElements(): array
    {
        return [
            'code' => '#setono_sylius_catalog_promotions_promotion_code',
            'name' => '#setono_sylius_catalog_promotions_promotion_name',

            'exclusive' => '#setono_sylius_catalog_promotions_promotion_exclusive',
            'priority' => '#setono_sylius_catalog_promotions_promotion_priority',

            'starts_at' => '#setono_sylius_catalog_promotions_promotion_startsAt',
            'starts_at_date' => '#setono_sylius_catalog_promotions_promotion_startsAt_date',
            'starts_at_time' => '#setono_sylius_catalog_promotions_promotion_startsAt_time',

            'ends_at' => '#setono_sylius_catalog_promotions_promotion_endsAt',
            'ends_at_date' => '#setono_sylius_catalog_promotions_promotion_endsAt_date',
            'ends_at_time' => '#setono_sylius_catalog_promotions_promotion_endsAt_time',

            'rules' => '#setono_sylius_catalog_promotions_promotion_rules',

            'action_type' => '#setono_sylius_catalog_promotions_promotion_actionType',
            'action_percent' => '#setono_sylius_catalog_promotions_promotion_actionPercent',
        ];
    }
}
