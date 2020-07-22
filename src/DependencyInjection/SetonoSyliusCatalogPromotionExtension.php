<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusCatalogPromotionExtension extends AbstractResourceExtension
{
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $this->registerResources('setono_sylius_catalog_promotion', $config['driver'], $config['resources'], $container);

        $env = $container->getParameter('kernel.environment');
        if ('test' === $env || 'test_cached' === $env) {
            $loader->load('test_services.xml');
        }
    }
}
