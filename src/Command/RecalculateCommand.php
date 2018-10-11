<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Command;

use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\ProductRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\SpecialRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Handler\ProductRecalculateHandlerInterface;
use Setono\SyliusBulkSpecialsPlugin\Handler\SpecialRecalculateHandlerInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

/**
 * Class RecalculateCommand
 */
class RecalculateCommand extends Command
{
    /**
     * @var SpecialRepositoryInterface
     */
    protected $specialRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var SpecialRecalculateHandlerInterface
     */
    protected $specialRecalculateHandler;

    /**
     * @var ProductRecalculateHandlerInterface
     */
    protected $productRecalculateHandler;

    /**
     * RecalculateCommand constructor.
     * @param SpecialRepositoryInterface $specialRepository
     * @param ProductRepositoryInterface $productRepository
     * @param SpecialRecalculateHandlerInterface $specialRecalculateHandler
     * @param ProductRecalculateHandlerInterface $productRecalculateHandler
     */
    public function __construct(
        SpecialRepositoryInterface $specialRepository,
        ProductRepositoryInterface $productRepository,
        SpecialRecalculateHandlerInterface $specialRecalculateHandler,
        ProductRecalculateHandlerInterface $productRecalculateHandler
    ) {
        $this->specialRepository = $specialRepository;
        $this->productRepository = $productRepository;
        $this->specialRecalculateHandler = $specialRecalculateHandler;
        $this->productRecalculateHandler = $productRecalculateHandler;

        parent::__construct(null);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('setono:sylius-bulk-specials:recalculate')
            ->addArgument(
                'kind',
                InputArgument::OPTIONAL,
                "One of 'special' or 'product'",
                'product'
            )
            ->addArgument(
                'identifier',
                InputArgument::OPTIONAL,
                'Special/Product identifier (ID or code). Optional for Products'
            )
            ->setDescription('Recalculate given Special-related Products or given Product. Pass no arguments to recalculate all products')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kind = $input->getArgument('kind');
        Assert::oneOf($kind, ['special', 'product']);

        $identifier = $input->getArgument('identifier');

        switch ($kind) {
            case 'special':
                if (is_null($identifier)) {
                    $output->writeln("<error>Identifier is mandatory for recalculating Specials</error>");
                    return;
                } elseif (is_integer($identifier)) {
                    $special = $this->specialRepository->findOneById($identifier);
                } else {
                    $special = $this->specialRepository->findOneByCode($identifier);
                }

                if (!$special instanceof SpecialInterface) {
                    $output->writeln(sprintf(
                        "Special with identifier '%s' was not found",
                        $identifier
                    ));
                    return;
                }

                $this->specialRecalculateHandler->handleSpecial($special);
                $output->writeln(sprintf(
                    "All products related to Special '%s' was recalculated",
                    (string) $special
                ));
                break;

            case 'product':
                if (is_null($identifier)) {
                    $products = $this->productRepository->findAll();
                } elseif (is_integer($identifier)) {
                    $products = $this->productRepository->findById($identifier);
                } else {
                    $products = $this->productRepository->findByCode($identifier);
                }

                if (!count($products)) {
                    $output->writeln("Products was not found");
                    return;
                }

                /** @var ProductInterface $product */
                foreach ($products as $product) {
                    $this->productRecalculateHandler->handleProduct($product);
                    $output->writeln(sprintf(
                        "Product price '%s' was recalculated based on previously assigned Specials",
                        (string) $product
                    ));
                }
                break;

            default:
                $output->writeln(sprintf(
                    "<error>Unknown argument value '%s'</error>",
                    $kind
                ));
        }

        $output->writeln('Done');
    }
}
