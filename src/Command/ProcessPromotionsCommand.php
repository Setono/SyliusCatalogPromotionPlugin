<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionsPlugin\Command;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\StringsException;
use function Safe\file_get_contents;
use function Safe\file_put_contents;
use function Safe\sprintf;
use Setono\SyliusCatalogPromotionsPlugin\Model\PromotionInterface;
use Setono\SyliusCatalogPromotionsPlugin\Repository\ChannelPricingRepositoryInterface;
use Setono\SyliusCatalogPromotionsPlugin\Repository\ProductRepositoryInterface;
use Setono\SyliusCatalogPromotionsPlugin\Repository\ProductVariantRepositoryInterface;
use Setono\SyliusCatalogPromotionsPlugin\Repository\PromotionRepositoryInterface;
use Setono\SyliusCatalogPromotionsPlugin\Rule\ManuallyDiscountedProductsExcludedRule;
use Setono\SyliusCatalogPromotionsPlugin\Rule\RuleInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;

final class ProcessPromotionsCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'setono:sylius-catalog-promotions:process';

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

    /**
     * @throws FilesystemException
     * @throws StringsException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->productVariantRepository instanceof EntityRepository) {
            throw new \RuntimeException(sprintf('The product variant repository is not an instance of %s', EntityRepository::class));
        }

        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $force = is_bool($input->getOption('force')) ? $input->getOption('force') : false;

        $lastExecution = $this->getLastExecution();

        $startTime = new DateTime();

        /** @var PromotionInterface[] $promotions */
        $promotions = $this->promotionRepository->findForProcessing();
        $promotionIds = array_map(static function (PromotionInterface $promotion) {
            return $promotion->getId();
        }, $promotions);

        if (!$force
            && null !== $lastExecution
            && $lastExecution['promotions'] === $promotionIds // means the last promotion ids were the exact same AND same order
            && !$this->hasAnyBeenUpdatedSince($lastExecution['start']) // despite being the same and same order we still need to check whether any relevant entities were updated
        ) {
            $output->writeln('Did not do nutting', OutputInterface::VERBOSITY_VERBOSE);

            return 0;
        }

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
                    $promotion->getMultiplier(), $productVariantIds, $promotion->getChannelCodes(), $startTime,
                    $promotion->isExclusive()
                );

                ++$i;
            } while (count($productVariantIds) !== 0);
        }

        $this->channelPricingRepository->updatePrices($startTime);

        $this->setExecution([
            'start' => $startTime,
            'end' => new DateTime(),
            'promotions' => $promotionIds,
        ]);

        return 0;
    }

    private function hasAnyBeenUpdatedSince(DateTimeInterface $dateTime): bool
    {
        return $this->productRepository->hasAnyBeenUpdatedSince($dateTime) ||
            $this->productVariantRepository->hasAnyBeenUpdatedSince($dateTime) ||
            $this->channelPricingRepository->hasAnyBeenUpdatedSince($dateTime) ||
            $this->promotionRepository->hasAnyBeenUpdatedSince($dateTime)
        ;
    }

    /**
     * @throws FilesystemException
     * @throws StringsException
     */
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

    /**
     * @throws FilesystemException
     * @throws StringsException
     */
    private function setExecution(array $execution): void
    {
        $filename = $this->getExecutionLogFilename();

        file_put_contents($filename, serialize($execution));
    }

    /**
     * @throws StringsException
     */
    private function getExecutionLogFilename(): string
    {
        return sprintf('%s/%s.log',
            $this->logsDir, Container::underscore(str_replace('\\', '', get_class($this)))
        );
    }
}
