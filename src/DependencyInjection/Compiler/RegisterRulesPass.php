<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\DependencyInjection\Compiler;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Webmozart\Assert\Assert;

final class RegisterRulesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $registry = $container->getDefinition('setono_sylius_catalog_promotion.registry.rule');
        $formRegistry = $container->getDefinition('setono_sylius_catalog_promotion.form_registry.rule');
        $formToLabelMap = [];

        foreach ($container->findTaggedServiceIds('setono_sylius_catalog_promotion.rule') as $id => $tagged) {
            /** @var array $attributes */
            foreach ($tagged as $attributes) {
                if (!isset($attributes['type'], $attributes['label'], $attributes['form_type'])) {
                    throw new InvalidArgumentException('Tagged rule `' . $id . '` needs to have `type`, `form_type` and `label` attributes.');
                }

                Assert::string($attributes['type']);
                Assert::string($attributes['label']);
                Assert::string($attributes['form_type']);

                $formToLabelMap[$attributes['type']] = $attributes['label'];
                $registry->addMethodCall('register', [$attributes['type'], new Reference($id)]);
                $formRegistry->addMethodCall('add', [$attributes['type'], 'default', $attributes['form_type']]);
            }
        }

        $container->setParameter('setono_sylius_catalog_promotion.promotion_rules', $formToLabelMap);
    }
}
