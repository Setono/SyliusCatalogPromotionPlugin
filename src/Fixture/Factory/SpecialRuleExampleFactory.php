<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Fixture\Factory;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialRuleInterface;
use Setono\SyliusBulkSpecialsPlugin\Special\Factory\SpecialRuleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecialRuleExampleFactory extends AbstractExampleFactory
{
    /** @var SpecialRuleFactoryInterface */
    protected $specialRuleFactory;

    /** @var array */
    protected $specialRules;

    /** @var OptionsResolver */
    protected $optionsResolver;

    public function __construct(SpecialRuleFactoryInterface $specialRuleFactory, array $specialRules)
    {
        $this->specialRuleFactory = $specialRuleFactory;
        $this->specialRules = $specialRules;

        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): SpecialRuleInterface
    {
        $options = $this->optionsResolver->resolve($options);

        return $this->specialRuleFactory->createByType(
            $options['type'],
            $options['configuration']
        );
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('type', function (): string {
                $specialRuleCodes = array_keys($this->specialRules);

                return $specialRuleCodes[array_rand($specialRuleCodes)];
            })
            ->setDefined('configuration')
            ->setAllowedTypes('configuration', ['string', 'array'])
        ;
    }
}
