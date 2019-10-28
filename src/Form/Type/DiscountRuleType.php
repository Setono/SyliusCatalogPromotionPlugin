<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

final class DiscountRuleType extends AbstractConfigurableDiscountElementType
{
    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('type', DiscountRuleChoiceType::class, [
                'label' => 'setono_sylius_bulk_discount.form.discount_rule.type',
                'attr' => [
                    'data-form-collection' => 'update',
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_bulk_discount_discount_rule';
    }
}
