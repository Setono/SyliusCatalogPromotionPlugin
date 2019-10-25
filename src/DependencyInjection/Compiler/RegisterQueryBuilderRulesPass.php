<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\DependencyInjection\Compiler;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterQueryBuilderRulesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $specialRuleQueryBuilderRegistry = $container->getDefinition('setono_sylius_bulk_specials.registry.query_builder_rule');

        foreach ($container->findTaggedServiceIds('setono_sylius_bulk_specials.query_builder_rule') as $id => $tagged) {
            foreach ($tagged as $attributes) {
                if (!isset($attributes['type'])) {
                    throw new InvalidArgumentException('Tagged rule query builder `' . $id . '` needs to have `type` attribute.');
                }

                $specialRuleQueryBuilderRegistry->addMethodCall('register', [$attributes['type'], new Reference($id)]);
            }
        }
    }
}
