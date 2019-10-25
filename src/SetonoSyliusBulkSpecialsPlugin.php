<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin;

use Setono\SyliusBulkSpecialsPlugin\DependencyInjection\Compiler\CompositeSpecialEligibilityCheckerPass;
use Setono\SyliusBulkSpecialsPlugin\DependencyInjection\Compiler\RegisterQueryBuilderRulesPass;
use Setono\SyliusBulkSpecialsPlugin\DependencyInjection\Compiler\RegisterRuleCheckersPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SetonoSyliusBulkSpecialsPlugin extends AbstractResourceBundle
{
    use SyliusPluginTrait;

    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CompositeSpecialEligibilityCheckerPass());
        $container->addCompilerPass(new RegisterRuleCheckersPass());
        $container->addCompilerPass(new RegisterQueryBuilderRulesPass());
    }
}
