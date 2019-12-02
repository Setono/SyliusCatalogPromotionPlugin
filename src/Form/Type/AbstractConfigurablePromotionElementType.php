<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Form\Type;

use Setono\SyliusCatalogPromotionPlugin\Model\ConfigurablePromotionElementInterface;
use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractConfigurablePromotionElementType extends AbstractResourceType
{
    /** @var FormTypeRegistryInterface */
    private $formTypeRegistry;

    public function __construct(string $dataClass, FormTypeRegistryInterface $formTypeRegistry, array $validationGroups = [])
    {
        parent::__construct($dataClass, $validationGroups);

        $this->formTypeRegistry = $formTypeRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $type = $this->getRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                $this->addConfigurationFields($event->getForm(), $this->formTypeRegistry->get($type, 'default'));
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $type = $this->getRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                $event->getForm()->get('type')->setData($type);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
                $data = $event->getData();

                if (!isset($data['type'])) {
                    return;
                }

                $this->addConfigurationFields($event->getForm(), $this->formTypeRegistry->get($data['type'], 'default'));
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('configuration_type', null)
            ->setAllowedTypes('configuration_type', ['string', 'null'])
        ;
    }

    protected function addConfigurationFields(FormInterface $form, string $configurationType): void
    {
        $form->add('configuration', $configurationType, [
            'label' => false,
        ]);
    }

    /**
     * @param ConfigurablePromotionElementInterface|null $data
     */
    protected function getRegistryIdentifier(FormInterface $form, $data = null): ?string
    {
        if ($data instanceof ConfigurablePromotionElementInterface && null !== $data->getType()) {
            return $data->getType();
        }

        if ($form->getConfig()->hasOption('configuration_type')) {
            return $form->getConfig()->getOption('configuration_type');
        }

        return null;
    }
}
