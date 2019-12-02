<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class PromotionType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('channels', ChannelChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label' => 'setono_sylius_catalog_promotion.form.promotion.channels',
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'label' => 'setono_sylius_catalog_promotion.form.promotion.name',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'setono_sylius_catalog_promotion.form.promotion.description',
                'required' => false,
            ])
            ->add('actionType', PromotionActionTypeChoiceType::class, [
                'label' => 'setono_sylius_catalog_promotion.form.promotion.action_type',
                'required' => true,
            ])
            ->add('actionPercent', TextType::class, [
                'label' => 'setono_sylius_catalog_promotion.form.promotion.action_percent',
                'required' => true,
            ])
            ->add('exclusive', CheckboxType::class, [
                'label' => 'setono_sylius_catalog_promotion.form.promotion.exclusive',
                'required' => false,
            ])
            ->add('manuallyDiscountedProductsExcluded', CheckboxType::class, [
                'label' => 'setono_sylius_catalog_promotion.form.promotion.manually_discounted_products_excluded',
                'required' => false,
            ])
            ->add('startsAt', DateTimeType::class, [
                'label' => 'setono_sylius_catalog_promotion.form.promotion.starts_at',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false,
            ])
            ->add('endsAt', DateTimeType::class, [
                'label' => 'setono_sylius_catalog_promotion.form.promotion.ends_at',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false,
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'setono_sylius_catalog_promotion.form.promotion.enabled',
                'required' => false,
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'setono_sylius_catalog_promotion.form.promotion.priority',
                'required' => false,
            ])
            ->add('rules', PromotionRuleCollectionType::class, [
                'label' => 'setono_sylius_catalog_promotion.form.promotion.rules',
                'button_add_label' => 'setono_sylius_catalog_promotion.form.promotion.add_rule',
                'required' => false,
            ])
            ->addEventSubscriber(new AddCodeFormSubscriber())
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_catalog_promotion_promotion';
    }
}
