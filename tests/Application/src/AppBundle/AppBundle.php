<?php

declare(strict_types=1);

namespace AppBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Safe\Exceptions\FilesystemException;
use function Safe\realpath;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppBundle extends Bundle
{
    /**
     * @throws FilesystemException
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver(
            [
                realpath(__DIR__ . '/Resources/config/doctrine/model') => 'AppBundle\Model',
            ],
            ['doctrine.orm.entity_manager']
        ));
    }
}
