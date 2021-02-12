<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Command;

use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Safe\DateTime;
use function Safe\file_get_contents;
use function Safe\file_put_contents;
use function Safe\sprintf;
use Setono\SyliusCatalogPromotionPlugin\Model\PromotionInterface;
use Setono\SyliusCatalogPromotionPlugin\Repository\ChannelPricingRepositoryInterface;
use Setono\SyliusCatalogPromotionPlugin\Repository\ProductRepositoryInterface;
use Setono\SyliusCatalogPromotionPlugin\Repository\ProductVariantRepositoryInterface;
use Setono\SyliusCatalogPromotionPlugin\Repository\PromotionRepositoryInterface;
use Setono\SyliusCatalogPromotionPlugin\Rule\ManuallyDiscountedProductsExcludedRule;
use Setono\SyliusCatalogPromotionPlugin\Rule\RuleInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Webmozart\Assert\Assert;

final class ProcessPromotionsCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'setono:sylius-catalog-promotion:process';

    /** @var ChannelPricingRepositoryInterface */
    private $channelPricingRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    /** @var PromotionRepositoryInterface */
    private $promotionRepository;

    /** @var ServiceRegistryInterface */
    private $ruleRegistry;

    /** @var string */
    private $logsDir;

    public function __construct(
        ChannelPricingRepositoryInterface $channelPricingRepository,
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        PromotionRepositoryInterface $promotionRepository,
        ServiceRegistryInterface $ruleRegistry,
        string $logsDir
    ) {
        parent::__construct();

        $this->channelPricingRepository = $channelPricingRepository;
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->promotionRepository = $promotionRepository;
        $this->ruleRegistry = $ruleRegistry;
        $this->logsDir = $logsDir;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Processes all promotions')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force the computation of promotions')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Assert::isInstanceOf($this->productVariantRepository, EntityRepository::class);

        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        /** @var bool $force */
        $force = $input->getOption('force');
        $startTime = new DateTime();

        /** @var PromotionInterface[] $promotions */
        $promotions = $this->promotionRepository->findForProcessing();
        $promotionIds = array_map(static function (PromotionInterface $promotion): int {
            return (int) $promotion->getId();
        }, $promotions);

        if (!$this->isProcessingAllowed($promotionIds) && !$force) {
            $output->writeln(
                'Nothing to process at the moment. Run command with --force option to force process',
                OutputInterface::VERBOSITY_VERBOSE
            );

            return 0;
        }

        $bulkIdentifier = uniqid('bulk-', true);

        $this->channelPricingRepository->resetMultiplier($startTime);

        foreach ($promotions as $promotion) {
            $qb = $this->productVariantRepository->createQueryBuilder('o');
            $qb->select('o.id');
            $qb->distinct();

            if ($promotion->isManuallyDiscountedProductsExcluded()) {
                (new ManuallyDiscountedProductsExcludedRule())->filter($qb, []);
            }

            foreach ($promotion->getRules() as $rule) {
                if ($rule->getType() === null) {
                    continue;
                }

                if (!$this->ruleRegistry->has($rule->getType())) {
                    // todo should this throw an exception or give an error somewhere?
                    continue;
                }

                /** @var RuleInterface $ruleQueryBuilder */
                $ruleQueryBuilder = $this->ruleRegistry->get($rule->getType());

                $ruleQueryBuilder->filter($qb, $rule->getConfiguration());
            }

            $bulkSize = 100;
            $qb->setMaxResults($bulkSize);
            $i = 0;

            do {
                $qb->setFirstResult($i * $bulkSize);
                $productVariantIds = $qb->getQuery()->getResult();

                $this->channelPricingRepository->updateMultiplier(
                    $promotion->getMultiplier(),
                    $productVariantIds,
                    $promotion->getChannelCodes(),
                    $startTime,
                    $bulkIdentifier,
                    $promotion->isExclusive(),
                    $promotion->isManuallyDiscountedProductsExcluded()
                );

                ++$i;
            } while (count($productVariantIds) !== 0);
        }

        $this->channelPricingRepository->updatePrices($startTime, $bulkIdentifier);

        $this->setExecution([
            'start' => $startTime,
            'end' => new DateTime(),
            'promotions' => $promotionIds,
        ]);

        return 0;
    }

    private function isProcessingAllowed(array $promotionIds): bool
    {
        // If there was no executions - we can process
        $lastExecution = $this->getLastExecution();
        if (null === $lastExecution) {
            return true;
        }

        // If last execution promotions not exact same as found for processing
        // or not at the same order - we can process
        if ($lastExecution['promotions'] !== $promotionIds) {
            return true;
        }

        // If any relevant entities were updated - we can process
        if ($this->hasAnyBeenUpdatedSince($lastExecution['start'])) {
            return true;
        }

        return false;
    }

    // As far as updatedAt field updating even when some fields not impacting promotion changed,
    // @todo Set catalog_promotions_process flag at memory storage / cache at subscribers once particular product/variant/taxon fields (that can impact promotion) changed
    private function hasAnyBeenUpdatedSince(DateTimeInterface $dateTime): bool
    {
        return $this->productRepository->hasAnyBeenUpdatedSince($dateTime) ||
            $this->productVariantRepository->hasAnyBeenUpdatedSince($dateTime) ||
            $this->channelPricingRepository->hasAnyBeenUpdatedSince($dateTime) ||
            $this->promotionRepository->hasAnyBeenUpdatedSince($dateTime)
        ;
    }

    // @todo Move storing last execution data to some memory storage / cache?
    private function getLastExecution(): ?array
    {
        $filename = $this->getExecutionLogFilename();

        if (!file_exists($filename)) {
            return null;
        }

        $execution = unserialize(file_get_contents($filename), [
            'allowed_classes' => true,
        ]);

        if (false === $execution) {
            return null;
        }

        // validate contents
        if (!isset($execution['start'], $execution['end'], $execution['promotions'])) {
            return null;
        }

        return $execution;
    }

    private function setExecution(array $execution): void
    {
        $filename = $this->getExecutionLogFilename();

        file_put_contents($filename, serialize($execution));
    }

    private function getExecutionLogFilename(): string
    {
        return sprintf('%s/%s.log',
            $this->logsDir, Container::underscore(str_replace('\\', '', get_class($this)))
        );
    }
}
