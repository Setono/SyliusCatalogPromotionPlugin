<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Form\Type\Special\Rule;

use Sylius\Bundle\ProductBundle\Form\Type\ProductAutocompleteChoiceType;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;

final class ContainsProductConfigurationType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $productRepository;

    /**
     * @param RepositoryInterface $productRepository
     */
    public function __construct(RepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product_code', ProductAutocompleteChoiceType::class, [
                'label' => 'setono_sylius_bulk_specials.form.special_rule.contains_product_configuration.product',
            ])
        ;

        $builder->get('product_code')->addModelTransformer(
            new ReversedTransformer(new ResourceToIdentifierTransformer($this->productRepository, 'code'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'setono_sylius_bulk_specials_special_rule_contains_product_configuration';
    }
}
