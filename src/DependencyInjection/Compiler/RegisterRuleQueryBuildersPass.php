<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterRuleQueryBuildersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $specialRuleQueryBuilderRegistry = $container->getDefinition('setono_sylius_bulk_specials.registry.special_rule_query_builder');

        foreach ($container->findTaggedServiceIds('setono_sylius_bulk_specials.special_rule_query_builder') as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                throw new \InvalidArgumentException('Tagged rule query builder `' . $id . '` needs to have `type` attribute.');
            }

            $specialRuleQueryBuilderRegistry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
        }
    }
}
