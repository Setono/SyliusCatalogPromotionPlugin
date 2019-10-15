<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\DependencyInjection;

use Enqueue\Bundle\EnqueueBundle;
use Exception;
use RuntimeException;
use function Safe\sprintf;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusBulkSpecialsExtension extends AbstractResourceExtension
{
    /**
     * @throws Exception
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loader->load(sprintf('services/integrations/%s.xml', $config['driver']));

        $this->registerResources('setono_sylius_bulk_specials', $config['driver'], $config['resources'], $container);

        if ($config['queue']) {
            if (!class_exists(EnqueueBundle::class)) {
                throw new RuntimeException('Unable to use queues as the enqueue/enqueue-bundle is not installed.');
            }

            // Load handler decorators to work asynchronously via enqueue
            $loader->load('services/handlers_async.xml');
        }

        $container->setParameter('setono_sylius_bulk_specials.queue', $config['queue']);

        $env = $container->getParameter('kernel.environment');
        if ('test' === $env || 'test_cached' === $env) {
            $loader->load('test_services.xml');
        }
    }
}
