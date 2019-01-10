<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class SpecialFixture extends AbstractResourceFixture
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'special';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->scalarNode('description')->cannotBeEmpty()->end()

                ->scalarNode('priority')->cannotBeEmpty()->end()
                ->booleanNode('exclusive')->end()

                ->scalarNode('starts_at')->cannotBeEmpty()->end()
                ->scalarNode('ends_at')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()

                ->scalarNode('action_type')->cannotBeEmpty()->end()
                ->scalarNode('action_percent')->cannotBeEmpty()->end()

                ->scalarNode('created_at')->cannotBeEmpty()->end()
                ->scalarNode('updated_at')->cannotBeEmpty()->end()

                ->arrayNode('rules')
                    ->requiresAtLeastOneElement()
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('type')->cannotBeEmpty()->end()
                            ->variableNode('configuration')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('channels')->scalarPrototype()->end()->end()
        ;
    }
}
