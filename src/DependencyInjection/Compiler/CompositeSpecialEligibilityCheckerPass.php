<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class CompositeSpecialEligibilityCheckerPass
 */
final class CompositeSpecialEligibilityCheckerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('setono_sylius_bulk_specials.special_eligibility_checker')) {
            return;
        }

        $container->getDefinition('setono_sylius_bulk_specials.special_eligibility_checker')->setArguments([
            array_map(
                function ($id) {
                    return new Reference($id);
                },
                array_keys($container->findTaggedServiceIds('setono_sylius_bulk_specials.special_eligibility_checker'))
            ),
        ]);
    }
}
