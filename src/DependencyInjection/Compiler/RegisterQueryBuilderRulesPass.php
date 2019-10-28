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
        $registry = $container->getDefinition('setono_sylius_bulk_specials.registry.query_builder_rule');
        $formRegistry = $container->getDefinition('setono_sylius_bulk_specials.form_registry.query_builder_rule');
        $formToLabelMap = [];

        foreach ($container->findTaggedServiceIds('setono_sylius_bulk_specials.query_builder_rule') as $id => $tagged) {
            foreach ($tagged as $attributes) {
                if (!isset($attributes['type'], $attributes['label'], $attributes['form_type'])) {
                    throw new InvalidArgumentException('Tagged query builder rule `' . $id . '` needs to have `type`, `form_type` and `label` attributes.');
                }

                $formToLabelMap[$attributes['type']] = $attributes['label'];
                $registry->addMethodCall('register', [$attributes['type'], new Reference($id)]);
                $formRegistry->addMethodCall('add', [$attributes['type'], 'default', $attributes['form_type']]);
            }
        }

        $container->setParameter('setono_sylius_bulk_specials.special_rules', $formToLabelMap);
    }
}