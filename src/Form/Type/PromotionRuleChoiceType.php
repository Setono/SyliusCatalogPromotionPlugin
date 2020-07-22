<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Form\Type;

use function Safe\array_flip;
use Safe\Exceptions\ArrayException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PromotionRuleChoiceType extends AbstractType
{
    /** @var array */
    private $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => array_flip($this->rules),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_catalog_promotion_promotion_rule_choice';
    }
}
