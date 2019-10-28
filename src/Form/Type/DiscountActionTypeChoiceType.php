<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Form\Type;

use Setono\SyliusBulkDiscountPlugin\Model\Discount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DiscountActionTypeChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
                'setono_sylius_bulk_discount.form.discount_action_type.choice.off' => Discount::ACTION_TYPE_OFF,
                'setono_sylius_bulk_discount.form.discount_action_type.choice.increase' => Discount::ACTION_TYPE_INCREASE,
            ],
            'empty_data' => Discount::ACTION_TYPE_OFF,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_bulk_discount_discount_action_type_choice';
    }
}
