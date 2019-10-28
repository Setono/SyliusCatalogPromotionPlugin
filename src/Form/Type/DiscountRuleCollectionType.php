<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Form\Type;

use Sylius\Bundle\PromotionBundle\Form\Type\Core\AbstractConfigurationCollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DiscountRuleCollectionType extends AbstractConfigurationCollectionType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('entry_type', DiscountRuleType::class);
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_bulk_discount_discount_rule_collection';
    }
}
