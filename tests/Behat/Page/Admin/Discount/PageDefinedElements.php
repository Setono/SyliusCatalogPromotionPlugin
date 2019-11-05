<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkDiscountPlugin\Behat\Page\Admin\Discount;

/**
 * Trait PageDefinedElements
 */
trait PageDefinedElements
{
    /**
     * @return array
     */
    protected function getDefinedElements(): array
    {
        return [
            'code' => '#setono_sylius_bulk_discount_discount_code',
            'name' => '#setono_sylius_bulk_discount_discount_name',

            'exclusive' => '#setono_sylius_bulk_discount_discount_exclusive',
            'priority' => '#setono_sylius_bulk_discount_discount_priority',

            'starts_at' => '#setono_sylius_bulk_discount_discount_startsAt',
            'starts_at_date' => '#setono_sylius_bulk_discount_discount_startsAt_date',
            'starts_at_time' => '#setono_sylius_bulk_discount_discount_startsAt_time',

            'ends_at' => '#setono_sylius_bulk_discount_discount_endsAt',
            'ends_at_date' => '#setono_sylius_bulk_discount_discount_endsAt_date',
            'ends_at_time' => '#setono_sylius_bulk_discount_discount_endsAt_time',

            'rules' => '#setono_sylius_bulk_discount_discount_rules',

            'action_type' => '#setono_sylius_bulk_discount_discount_actionType',
            'action_percent' => '#setono_sylius_bulk_discount_discount_actionPercent',
        ];
    }
}
