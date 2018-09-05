<?php

namespace Setono\SyliusBulkSpecialsPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $marketingSubmenu = $menu->getChild('marketing');
        $marketingSubmenu
            ->addChild('specials', [
                'route'=>'setono_sylius_bulk_specials_admin_specials_index'
            ])
            ->setAttribute('type', 'link')
            ->setLabel('setono_sylius_bulk_specials.menu.admin.main.marketing.specials')
            ->setLabelAttributes([
                'icon'=>'tasks'
            ])
        ;
    }
}