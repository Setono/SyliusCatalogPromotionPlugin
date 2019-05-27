<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Form\Type;

use Setono\SyliusBulkSpecialsPlugin\Model\Special;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SpecialActionTypeChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
                'setono_sylius_bulk_specials.form.special_action_type.choice.off' => Special::ACTION_TYPE_OFF,
                'setono_sylius_bulk_specials.form.special_action_type.choice.increase' => Special::ACTION_TYPE_INCREASE,
            ],
            'empty_data' => Special::ACTION_TYPE_OFF,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_bulk_specials_special_action_type_choice';
    }
}
