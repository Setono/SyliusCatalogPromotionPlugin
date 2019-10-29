<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Fixture\Factory;

use DateTime;
use DateTimeInterface;
use Exception;
use Faker\Generator;
use Setono\SyliusBulkDiscountPlugin\Model\Discount;
use Setono\SyliusBulkDiscountPlugin\Model\DiscountInterface;
use Setono\SyliusBulkDiscountPlugin\Model\DiscountRuleInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscountExampleFactory extends AbstractExampleFactory
{
    /** @var ChannelRepositoryInterface */
    protected $channelRepository;

    /** @var Factory */
    protected $discountFactory;

    /** @var DiscountRuleExampleFactory */
    protected $discountRuleExampleFactory;

    /** @var Generator */
    protected $faker;

    /** @var OptionsResolver */
    protected $optionsResolver;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        Factory $discountFactory,
        DiscountRuleExampleFactory $discountRuleExampleFactory
    ) {
        $this->channelRepository = $channelRepository;
        $this->discountFactory = $discountFactory;
        $this->discountRuleExampleFactory = $discountRuleExampleFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * @throws Exception
     */
    public function create(array $options = []): DiscountInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var DiscountInterface $discount */
        $discount = $this->discountFactory->createNew();
        $discount->setCode($options['code']);
        $discount->setName($options['name']);
        $discount->setDescription($options['description']);

        $discount->setPriority((int) $options['priority']);
        $discount->setExclusive($options['exclusive']);

        if (isset($options['starts_at'])) {
            $discount->setStartsAt(new DateTime($options['starts_at']));
        }

        if (isset($options['ends_at'])) {
            $discount->setEndsAt(new DateTime($options['ends_at']));
        }
        $discount->setEnabled($options['enabled']);

        foreach ($options['channels'] as $channel) {
            $discount->addChannel($channel);
        }

        foreach ($options['rules'] as $ruleOptions) {
            /** @var DiscountRuleInterface $discountRule */
            $discountRule = $this->discountRuleExampleFactory->create($ruleOptions);
            $discount->addRule($discountRule);
        }

        $discount->setActionType($options['action_type']);
        $discount->setActionPercent($options['action_percent']);

        $discount->setCreatedAt($options['created_at']);
        $discount->setUpdatedAt($options['updated_at']);

        return $discount;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('code', static function (Options $options): string {
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
            ->setDefault('enabled', function (): bool {
                return $this->faker->boolean(90);
            })
            ->setAllowedTypes('enabled', 'bool')

            ->setDefault('action_type', static function () {
                $actionTypes = Discount::getActionTypes();

                return $actionTypes[array_rand($actionTypes)];
            })
            ->setDefault('action_percent', static function (): int {
                return 10 * random_int(1, 9);
            })
            ->setAllowedTypes('action_percent', 'int')

            ->setDefault('created_at', null)
            ->setAllowedTypes('created_at', ['null', DateTimeInterface::class])
            ->setDefault('updated_at', null)
            ->setAllowedTypes('updated_at', ['null', DateTimeInterface::class])

            ->setDefined('rules')
            ->setNormalizer('rules', static function (Options $options, array $rules): array {
                if (count($rules) === 0) {
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
