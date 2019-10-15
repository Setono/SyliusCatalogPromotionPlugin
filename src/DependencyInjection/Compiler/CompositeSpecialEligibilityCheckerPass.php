<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class CompositeSpecialEligibilityCheckerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->getDefinition('setono_sylius_bulk_specials.special_eligibility_checker')->setArguments([
            array_map(
                static function ($id) {
                    return new Reference($id);
                },
                array_keys($container->findTaggedServiceIds('setono_sylius_bulk_specials.special_eligibility_checker'))
            ),
        ]);
    }
}
