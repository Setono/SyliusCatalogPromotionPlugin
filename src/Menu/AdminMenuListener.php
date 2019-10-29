<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $marketingSubmenu = $menu->getChild('marketing');
        if (!$marketingSubmenu instanceof ItemInterface) {
            return;
        }

        $marketingSubmenu
            ->addChild('discounts', [
                'route' => 'setono_sylius_bulk_discount_admin_discount_index',
            ])
            ->setAttribute('type', 'link')
            ->setLabel('setono_sylius_bulk_discount.menu.admin.main.marketing.discounts')
            ->setLabelAttributes([
                'icon' => 'tasks',
            ])
        ;
    }
}
