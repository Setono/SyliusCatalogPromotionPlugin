<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

final class SpecialRuleType extends AbstractConfigurableSpecialElementType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('type', SpecialRuleChoiceType::class, [
                'label' => 'setono_sylius_bulk_specials.form.special_rule.type',
                'attr' => [
                    'data-form-collection' => 'update',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'setono_sylius_bulk_specials_special_rule';
    }
}
