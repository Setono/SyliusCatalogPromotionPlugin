<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionsPlugin\Menu;

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
            ->addChild('catalog_promotions', [
                'route' => 'setono_sylius_catalog_promotions_admin_promotion_index',
            ])
            ->setAttribute('type', 'link')
            ->setLabel('setono_sylius_catalog_promotions.menu.admin.main.marketing.promotions')
            ->setLabelAttributes([
                'icon' => 'tasks',
            ])
        ;
    }
}
