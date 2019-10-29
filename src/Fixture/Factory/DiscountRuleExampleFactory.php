<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Fixture\Factory;

use Setono\SyliusBulkDiscountPlugin\Factory\DiscountRuleFactoryInterface;
use Setono\SyliusBulkDiscountPlugin\Model\DiscountRuleInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscountRuleExampleFactory extends AbstractExampleFactory
{
    /** @var DiscountRuleFactoryInterface */
    protected $discountRuleFactory;

    /** @var array */
    protected $discountRules;

    /** @var OptionsResolver */
    protected $optionsResolver;

    public function __construct(DiscountRuleFactoryInterface $discountRuleFactory, array $discountRules)
    {
        $this->discountRuleFactory = $discountRuleFactory;
        $this->discountRules = $discountRules;

        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): DiscountRuleInterface
    {
        $options = $this->optionsResolver->resolve($options);

        return $this->discountRuleFactory->createByType(
            $options['type'],
            $options['configuration']
        );
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('type', function (): string {
                $discountRuleCodes = array_keys($this->discountRules);

                return $discountRuleCodes[array_rand($discountRuleCodes)];
            })
            ->setDefined('configuration')
            ->setAllowedTypes('configuration', ['string', 'array'])
        ;
    }
}
