<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RegisterRuleCheckersPass
 */
final class RegisterRuleCheckersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('setono_sylius_bulk_specials.registry_special_rule_checker') || !$container->has('setono_sylius_bulk_specials.form_registry.special_rule_checker')) {
            return;
        }

        $specialRuleCheckerRegistry = $container->getDefinition('setono_sylius_bulk_specials.registry_special_rule_checker');
        $specialRuleCheckerFormTypeRegistry = $container->getDefinition('setono_sylius_bulk_specials.form_registry.special_rule_checker');

        $specialRuleCheckerTypeToLabelMap = [];
        foreach ($container->findTaggedServiceIds('setono_sylius_bulk_specials.special_rule_checker') as $id => $attributes) {
            if (!isset($attributes[0]['type'], $attributes[0]['label'], $attributes[0]['form_type'])) {
                throw new \InvalidArgumentException('Tagged rule checker `' . $id . '` needs to have `type`, `form_type` and `label` attributes.');
            }

            $specialRuleCheckerTypeToLabelMap[$attributes[0]['type']] = $attributes[0]['label'];
            $specialRuleCheckerRegistry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
            $specialRuleCheckerFormTypeRegistry->addMethodCall('add', [$attributes[0]['type'], 'default', $attributes[0]['form_type']]);
        }

        $container->setParameter('setono_sylius_bulk_specials.special_rules', $specialRuleCheckerTypeToLabelMap);
    }
}
