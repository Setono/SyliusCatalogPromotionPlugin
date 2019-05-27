<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Menu;

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
            ->addChild('specials', [
                'route' => 'setono_sylius_bulk_specials_admin_special_index',
            ])
            ->setAttribute('type', 'link')
            ->setLabel('setono_sylius_bulk_specials.menu.admin.main.marketing.specials')
            ->setLabelAttributes([
                'icon' => 'tasks',
            ])
        ;
    }
}
