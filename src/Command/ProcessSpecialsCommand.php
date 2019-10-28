<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Command;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\StringsException;
use function Safe\file_get_contents;
use function Safe\file_put_contents;
use function Safe\sprintf;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Setono\SyliusBulkSpecialsPlugin\QueryBuilderRule\ManuallyDiscountedProductsExcludedQueryBuilderRule;
use Setono\SyliusBulkSpecialsPlugin\QueryBuilderRule\QueryBuilderRuleInterface;
use Setono\SyliusBulkSpecialsPlugin\Repository\ChannelPricingRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Repository\ProductRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Repository\ProductVariantRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Repository\SpecialRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Webmozart\Assert\Assert;

final class ProcessSpecialsCommand extends Command
{
    use LockableTrait;

    /** @var ChannelPricingRepositoryInterface */
    private $channelPricingRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    /** @var SpecialRepositoryInterface */
    private $specialRepository;

    /** @var ServiceRegistryInterface */
    private $queryBuilderRuleRegistry;

    /** @var string */
    private $logsDir;

    public function __construct(
        ChannelPricingRepositoryInterface $channelPricingRepository,
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        SpecialRepositoryInterface $specialRepository,
        ServiceRegistryInterface $queryBuilderRuleRegistry,
        string $logsDir
    ) {
        parent::__construct();

        $this->channelPricingRepository = $channelPricingRepository;
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->specialRepository = $specialRepository;
        $this->queryBuilderRuleRegistry = $queryBuilderRuleRegistry;
        $this->logsDir = $logsDir;
    }

    protected function configure(): void
    {
        $this
            ->setName('setono:sylius-bulk-specials:process')
            ->setDescription('Processes all specials')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force the computation of specials')
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

        /** @var SpecialInterface[] $specials */
        $specials = $this->specialRepository->findForProcessing();
        $specialIds = array_map(static function (SpecialInterface $special) {
            return $special->getId();
        }, $specials);

        if (!$force
            && null !== $lastExecution
            && $lastExecution['specials'] === $specialIds // means the last special ids were the exact same AND same order
            && !$this->hasAnyBeenUpdatedSince($lastExecution['start']) // despite being the same and same order we still need to check whether any relevant entities were updated
        ) {
            $output->writeln('Did not do nutting', OutputInterface::VERBOSITY_VERBOSE);

            return 0;
        }

        $this->channelPricingRepository->resetMultiplier($startTime);

        foreach ($specials as $special) {
            $qb = $this->productVariantRepository->createQueryBuilder('o');
            if ($special->isManuallyDiscountedProductsExcluded()) {
                (new ManuallyDiscountedProductsExcludedQueryBuilderRule())->filter($qb, []);
            }

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
                $special->getMultiplier(), $qb, $special->getChannelCodes(), $startTime, $special->isExclusive()
            );
        }

        $this->channelPricingRepository->updatePrices($startTime);

        $this->setExecution([
            'start' => $startTime,
            'end' => new DateTime(),
            'specials' => $specialIds,
        ]);

        return 0;
    }

    private function hasAnyBeenUpdatedSince(DateTimeInterface $dateTime): bool
    {
        return $this->productRepository->hasAnyBeenUpdatedSince($dateTime) ||
            $this->productVariantRepository->hasAnyBeenUpdatedSince($dateTime) ||
            $this->channelPricingRepository->hasAnyBeenUpdatedSince($dateTime) ||
            $this->specialRepository->hasAnyBeenUpdatedSince($dateTime)
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
        if (!isset($execution['start'], $execution['end'], $execution['specials'])) {
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
