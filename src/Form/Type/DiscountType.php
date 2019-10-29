<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class DiscountType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('channels', ChannelChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label' => 'setono_sylius_bulk_discount.form.discount.channels',
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'label' => 'setono_sylius_bulk_discount.form.discount.name',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'setono_sylius_bulk_discount.form.discount.description',
                'required' => false,
            ])
            ->add('actionType', DiscountActionTypeChoiceType::class, [
                'label' => 'setono_sylius_bulk_discount.form.discount.action_type',
                'required' => true,
            ])
            ->add('actionPercent', TextType::class, [
                'label' => 'setono_sylius_bulk_discount.form.discount.action_percent',
                'required' => true,
            ])
            ->add('exclusive', CheckboxType::class, [
                'label' => 'setono_sylius_bulk_discount.form.discount.exclusive',
                'required' => false,
            ])
            ->add('manuallyDiscountedProductsExcluded', CheckboxType::class, [
                'label' => 'setono_sylius_bulk_discount.form.discount.manually_discounted_products_excluded',
                'required' => false,
            ])
            ->add('startsAt', DateTimeType::class, [
                'label' => 'setono_sylius_bulk_discount.form.discount.starts_at',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false,
            ])
            ->add('endsAt', DateTimeType::class, [
                'label' => 'setono_sylius_bulk_discount.form.discount.ends_at',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false,
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'setono_sylius_bulk_discount.form.discount.enabled',
                'required' => false,
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'setono_sylius_bulk_discount.form.discount.priority',
                'required' => false,
            ])
            ->add('rules', DiscountRuleCollectionType::class, [
                'label' => 'setono_sylius_bulk_discount.form.discount.rules',
                'button_add_label' => 'setono_sylius_bulk_discount.form.discount.add_rule',
                'required' => false,
            ])
            ->addEventSubscriber(new AddCodeFormSubscriber())
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_bulk_discount_discount';
    }
}
