<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Command;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\StringsException;
use function Safe\file_get_contents;
use function Safe\file_put_contents;
use function Safe\sprintf;
use Setono\SyliusBulkDiscountPlugin\Model\DiscountInterface;
use Setono\SyliusBulkDiscountPlugin\Repository\ChannelPricingRepositoryInterface;
use Setono\SyliusBulkDiscountPlugin\Repository\DiscountRepositoryInterface;
use Setono\SyliusBulkDiscountPlugin\Repository\ProductRepositoryInterface;
use Setono\SyliusBulkDiscountPlugin\Repository\ProductVariantRepositoryInterface;
use Setono\SyliusBulkDiscountPlugin\Rule\ManuallyDiscountedProductsExcludedRule;
use Setono\SyliusBulkDiscountPlugin\Rule\RuleInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Webmozart\Assert\Assert;

final class ProcessDiscountsCommand extends Command
{
    use LockableTrait;

    /** @var ChannelPricingRepositoryInterface */
    private $channelPricingRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    /** @var DiscountRepositoryInterface */
    private $discountRepository;

    /** @var ServiceRegistryInterface */
    private $ruleRegistry;

    /** @var string */
    private $logsDir;

    public function __construct(
        ChannelPricingRepositoryInterface $channelPricingRepository,
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        DiscountRepositoryInterface $discountRepository,
        ServiceRegistryInterface $ruleRegistry,
        string $logsDir
    ) {
        parent::__construct();

        $this->channelPricingRepository = $channelPricingRepository;
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->discountRepository = $discountRepository;
        $this->ruleRegistry = $ruleRegistry;
        $this->logsDir = $logsDir;
    }

    protected function configure(): void
    {
        $this
            ->setName('setono:sylius-bulk-discount:process')
            ->setDescription('Processes all discounts')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force the computation of discounts')
        ;
    }

    /**
     * @throws FilesystemException
     * @throws StringsException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Assert::isInstanceOf($this->productVariantRepository, EntityRepository::class);

        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $force = is_bool($input->getOption('force')) ? $input->getOption('force') : false;

        $lastExecution = $this->getLastExecution();

        $startTime = new DateTime();

        /** @var DiscountInterface[] $discounts */
        $discounts = $this->discountRepository->findForProcessing();
        $discountIds = array_map(static function (DiscountInterface $discount) {
            return $discount->getId();
        }, $discounts);

        if (!$force
            && null !== $lastExecution
            && $lastExecution['discounts'] === $discountIds // means the last discount ids were the exact same AND same order
            && !$this->hasAnyBeenUpdatedSince($lastExecution['start']) // despite being the same and same order we still need to check whether any relevant entities were updated
        ) {
            $output->writeln('Did not do nutting', OutputInterface::VERBOSITY_VERBOSE);

            return 0;
        }

        $this->channelPricingRepository->resetMultiplier($startTime);

        foreach ($discounts as $discount) {
            $qb = $this->productVariantRepository->createQueryBuilder('o');
            if ($discount->isManuallyDiscountedProductsExcluded()) {
                (new ManuallyDiscountedProductsExcludedRule())->filter($qb, []);
            }

            foreach ($discount->getRules() as $rule) {
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

            $this->channelPricingRepository->updateMultiplier(
                $discount->getMultiplier(), $qb, $discount->getChannelCodes(), $startTime, $discount->isExclusive()
            );
        }

        $this->channelPricingRepository->updatePrices($startTime);

        $this->setExecution([
            'start' => $startTime,
            'end' => new DateTime(),
            'discounts' => $discountIds,
        ]);

        return 0;
    }

    private function hasAnyBeenUpdatedSince(DateTimeInterface $dateTime): bool
    {
        return $this->productRepository->hasAnyBeenUpdatedSince($dateTime) ||
            $this->productVariantRepository->hasAnyBeenUpdatedSince($dateTime) ||
            $this->channelPricingRepository->hasAnyBeenUpdatedSince($dateTime) ||
            $this->discountRepository->hasAnyBeenUpdatedSince($dateTime)
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
        if (!isset($execution['start'], $execution['end'], $execution['discounts'])) {
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
        return sprintf('%s/%s.log', $this->logsDir,
            Container::underscore(str_replace('\\', '', get_class($this))));
    }
}
