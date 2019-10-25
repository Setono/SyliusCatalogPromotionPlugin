<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Command;

use Doctrine\ORM\EntityRepository;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\SpecialRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\QueryBuilder\Rule\QueryBuilderRuleInterface;
use Setono\SyliusBulkSpecialsPlugin\Repository\ChannelPricingRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

class ProcessSpecialsCommand extends Command
{
    /** @var ChannelPricingRepositoryInterface */
    private $channelPricingRepository;

    /** @var ProductVariantRepositoryInterface|EntityRepository */
    private $productVariantRepository;

    /** @var SpecialRepositoryInterface */
    private $specialRepository;

    /** @var ServiceRegistryInterface */
    private $queryBuilderRuleRegistry;

    public function __construct(
        ChannelPricingRepositoryInterface $channelPricingRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        SpecialRepositoryInterface $specialRepository,
        ServiceRegistryInterface $queryBuilderRuleRegistry
    ) {
        parent::__construct();

        Assert::isInstanceOf($productVariantRepository, EntityRepository::class);

        $this->channelPricingRepository = $channelPricingRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->specialRepository = $specialRepository;
        $this->queryBuilderRuleRegistry = $queryBuilderRuleRegistry;
    }

    protected function configure(): void
    {
        $this
            ->setName('setono:sylius-bulk-specials:process')
            ->setDescription('Processes all specials');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->channelPricingRepository->resetMultiplier();

        $this->handleSpecials($this->specialRepository->findNonExclusiveEnabledWithAtLeastOneChannel(), false);
        $this->handleSpecials(
            $this->specialRepository->findExclusiveEnabledWithAtLeastOneChannelOrderedByPriorityAscending(), true
        );

        $this->channelPricingRepository->updatePrices();

        return 0;
    }

    private function handleSpecials(array $specials, bool $exclusive): void
    {
        foreach ($specials as $special) {
            $qb = $this->productVariantRepository->createQueryBuilder('o');

            foreach ($special->getRules() as $rule) {
                if ($rule->getType() === null) {
                    continue;
                }

                if (!$this->queryBuilderRuleRegistry->has($rule->getType())) {
                    // todo should this throw an exception or give an error somewhere?
                    continue;
                }

                /** @var QueryBuilderRuleInterface $ruleQueryBuilder */
                $ruleQueryBuilder = $this->queryBuilderRuleRegistry->get($rule->getType());

                $ruleQueryBuilder->filter($qb, $rule->getConfiguration());
            }

            $this->channelPricingRepository->updateMultiplier(
                $special->getMultiplier(), $qb, $special->getChannelCodes(), $exclusive
            );
        }
    }
}
