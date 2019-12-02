<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

final class PromotionRuleType extends AbstractConfigurablePromotionElementType
{
    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('type', PromotionRuleChoiceType::class, [
                'label' => 'setono_sylius_catalog_promotion.form.promotion_rule.type',
                'attr' => [
                    'data-form-collection' => 'update',
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_catalog_promotion_promotion_rule';
    }
}
