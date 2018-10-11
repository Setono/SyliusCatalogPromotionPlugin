<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Command;

use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\ProductRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Handler\ProductRecalculateHandlerInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RecalculateProductCommand
 */
class RecalculateProductCommand extends Command
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ProductRecalculateHandlerInterface
     */
    protected $productRecalculateHandler;

    /**
     * RecalculateProductCommand constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param ProductRecalculateHandlerInterface $productRecalculateHandler
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductRecalculateHandlerInterface $productRecalculateHandler
    ) {
        $this->productRepository = $productRepository;
        $this->productRecalculateHandler = $productRecalculateHandler;

        parent::__construct(null);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('setono:sylius-bulk-specials:recalculate-product')
            ->addArgument(
                'identifier',
                InputArgument::OPTIONAL,
                'Product identifier (ID or code)'
            )
            ->setDescription('Recalculate given Product. Pass no arguments to recalculate all Products')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $identifier = $input->getArgument('identifier');

        if (is_null($identifier)) {
            $products = $this->productRepository->findAll();
        } else {
            $products = $this->productRepository->findBy([
                is_numeric($identifier) ? 'id' : 'code' => $identifier
            ]);
        }

        if (!count($products)) {
            $output->writeln("<error>Products was not found</error>");
            return 0;
        }

        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $this->productRecalculateHandler->handleProduct($product);
            $output->writeln(sprintf(
                "<info>Price for Product '%s' was recalculated based on previously assigned Specials</info>",
                (string) $product
            ));
        }

        $output->writeln('Done');
    }
}
