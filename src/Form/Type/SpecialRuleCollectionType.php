<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Form\Type;

use Sylius\Bundle\PromotionBundle\Form\Type\Core\AbstractConfigurationCollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SpecialRuleCollectionType extends AbstractConfigurationCollectionType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('entry_type', SpecialRuleType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'setono_sylius_bulk_specials_special_rule_collection';
    }
}
