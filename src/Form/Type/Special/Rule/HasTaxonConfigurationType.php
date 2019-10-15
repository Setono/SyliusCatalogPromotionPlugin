<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Form\Type\Special\Rule;

use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonAutocompleteChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class HasTaxonConfigurationType extends AbstractType
{
    /** @var DataTransformerInterface */
    private $taxonsToCodesTransformer;

    public function __construct(DataTransformerInterface $taxonsToCodesTransformer)
    {
        $this->taxonsToCodesTransformer = $taxonsToCodesTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taxons', TaxonAutocompleteChoiceType::class, [
                'label' => 'setono_sylius_bulk_specials.form.special_rule.has_taxon_configuration.taxons',
                'multiple' => true,
            ])
        ;

        $builder->get('taxons')->addModelTransformer($this->taxonsToCodesTransformer);
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_bulk_specials_special_rule_has_taxon_configuration';
    }
}
