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
     *
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
    protected function configure(): void
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
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $identifier = $input->getArgument('identifier');
        if (null === $identifier) {
            $products = $this->productRepository->findAll();
        } else {
            $products = $this->productRepository->findBy([
                is_numeric($identifier) ? 'id' : 'code' => $identifier,
            ]);
        }

        if (!count($products)) {
            $output->writeln('<error>Products was not found</error>');

            return;
        }

        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $this->eligibleSpecialsReassignHandler->handleProduct($product);
            $output->writeln(sprintf(
                "<info>Eligible Specials was reassigned to Product '%s'</info>",
                (string) $product
            ));
        }

        $output->writeln('Done');
    }
}
