<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class SpecialType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('channels', ChannelChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label' => 'setono_sylius_bulk_specials.form.special.channels',
            ])
            ->add('name', TextType::class, [
                'label' => 'setono_sylius_bulk_specials.form.special.name',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'setono_sylius_bulk_specials.form.special.description',
                'required' => false,
            ])
            ->add('actionType', SpecialActionTypeChoiceType::class, [
                'label' => 'setono_sylius_bulk_specials.form.special.action_type',
                'required' => true,
            ])
            ->add('actionPercent', TextType::class, [
                'label' => 'setono_sylius_bulk_specials.form.special.action_percent',
                'required' => true,
            ])
            ->add('exclusive', CheckboxType::class, [
                'label' => 'setono_sylius_bulk_specials.form.special.exclusive',
            ])
            ->add('manuallyDiscountedProductsExcluded', CheckboxType::class, [
                'label' => 'setono_sylius_bulk_specials.form.special.manually_discounted_products_excluded',
            ])
            ->add('startsAt', DateTimeType::class, [
                'label' => 'setono_sylius_bulk_specials.form.special.starts_at',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false,
            ])
            ->add('endsAt', DateTimeType::class, [
                'label' => 'setono_sylius_bulk_specials.form.special.ends_at',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false,
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'setono_sylius_bulk_specials.form.special.enabled',
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'setono_sylius_bulk_specials.form.special.priority',
                'required' => false,
            ])
            ->add('rules', SpecialRuleCollectionType::class, [
                'label' => 'setono_sylius_bulk_specials.form.special.rules',
                'button_add_label' => 'setono_sylius_bulk_specials.form.special.add_rule',
            ])
            ->addEventSubscriber(new AddCodeFormSubscriber())
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_bulk_specials_special';
    }
}
