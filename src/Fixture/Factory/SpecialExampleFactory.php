<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Fixture\Factory;

use Setono\SyliusBulkSpecialsPlugin\Model\Special;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialRuleInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecialExampleFactory extends AbstractExampleFactory
{
    /**
     * @var ChannelRepositoryInterface
     */
    protected $channelRepository;

    /**
     * @var Factory
     */
    protected $specialFactory;

    /**
     * @var SpecialRuleExampleFactory
     */
    protected $specialRuleExampleFactory;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var OptionsResolver
     */
    protected $optionsResolver;

    /**
     * SpecialExampleFactory constructor.
     *
     * @param ChannelRepositoryInterface $channelRepository
     * @param Factory $specialFactory
     * @param SpecialRuleExampleFactory $specialRuleExampleFactory
     */
    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        Factory $specialFactory,
        SpecialRuleExampleFactory $specialRuleExampleFactory
    ) {
        $this->channelRepository = $channelRepository;
        $this->specialFactory = $specialFactory;
        $this->specialRuleExampleFactory = $specialRuleExampleFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): specialInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var SpecialInterface $special */
        $special = $this->specialFactory->createNew();
        $special->setCode($options['code']);
        $special->setName($options['name']);
        $special->setDescription($options['description']);

        $special->setPriority((int) $options['priority']);
        $special->setExclusive($options['exclusive']);

        if (isset($options['starts_at'])) {
            $special->setStartsAt(new \DateTime($options['starts_at']));
        }

        if (isset($options['ends_at'])) {
            $special->setEndsAt(new \DateTime($options['ends_at']));
        }
        $special->setEnabled($options['enabled']);

        foreach ($options['channels'] as $channel) {
            $special->addChannel($channel);
        }

        foreach ($options['rules'] as $ruleOptions) {
            /** @var SpecialRuleInterface $specialRule */
            $specialRule = $this->specialRuleExampleFactory->create($ruleOptions);
            $special->addRule($specialRule);
        }

        $special->setActionType($options['action_type']);
        $special->setActionPercent($options['action_percent']);

        $special->setCreatedAt($options['created_at']);
        $special->setUpdatedAt($options['updated_at']);

        return $special;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('code', function (Options $options): string {
                return StringInflector::nameToCode($options['name']);
            })
            ->setDefault('name', $this->faker->words(3, true))
            ->setDefault('description', $this->faker->sentence())

            ->setDefault('priority', 0)
            ->setAllowedTypes('priority', 'int')
            ->setDefault('exclusive', $this->faker->boolean(25))
            ->setAllowedTypes('exclusive', 'bool')

            ->setDefault('starts_at', null)
            ->setAllowedTypes('starts_at', ['null', 'string'])
            ->setDefault('ends_at', null)
            ->setAllowedTypes('ends_at', ['null', 'string'])
            ->setDefault('enabled', function (Options $options): bool {
                return $this->faker->boolean(90);
            })
            ->setAllowedTypes('enabled', 'bool')

            ->setDefault('action_type', function (Options $options) {
                $actionTypes = Special::getActionTypes();

                return $actionTypes[array_rand($actionTypes)];
            })
            ->setDefault('action_percent', function (Options $options): int {
                return 10 * random_int(1, 9);
            })
            ->setAllowedTypes('action_percent', 'int')

            ->setDefault('created_at', null)
            ->setAllowedTypes('created_at', ['null', \DateTimeInterface::class])
            ->setDefault('updated_at', null)
            ->setAllowedTypes('updated_at', ['null', \DateTimeInterface::class])

            ->setDefined('rules')
            ->setNormalizer('rules', function (Options $options, array $rules): array {
                if (empty($rules)) {
                    return [[]];
                }

                return $rules;
            })

            ->setDefault('channels', LazyOption::all($this->channelRepository))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))
        ;
    }
}
