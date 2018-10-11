<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Command;

use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\ProductRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Handler\EligibleSpecialsReassignHandlerInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReassignCommand
 */
class ReassignCommand extends Command
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var EligibleSpecialsReassignHandlerInterface
     */
    protected $eligibleSpecialsReassignHandler;

    /**
     * ReassignCommand constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param EligibleSpecialsReassignHandlerInterface $eligibleSpecialsReassignHandler
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        EligibleSpecialsReassignHandlerInterface $eligibleSpecialsReassignHandler
    ) {
        $this->productRepository = $productRepository;
        $this->eligibleSpecialsReassignHandler = $eligibleSpecialsReassignHandler;

        parent::__construct(null);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('setono:sylius-bulk-specials:reassign')
            ->addArgument(
                'identifier',
                InputArgument::OPTIONAL,
                'Product identifier (ID or code)'
            )
            ->setDescription('Reassign specials to given Product or to all products')
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
            $this->eligibleSpecialsReassignHandler->handleProduct($product);
            $output->writeln(sprintf(
                "Eligible Specials was reassigned to Product '%s'",
                (string) $product
            ));
        }

        $output->writeln('Done');
    }
}
