<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Command;

use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\ProductRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Message\Command\AssignEligibleSpecials;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * This command will assign eligible specials to one or all products
 */
class AssignSpecialsCommand extends Command
{
    /** @var MessageBusInterface */
    private $commandBus;

    /** @var ProductRepositoryInterface */
    protected $productRepository;

    public function __construct(
        MessageBusInterface $commandBus,
        ProductRepositoryInterface $productRepository
    ) {
        $this->commandBus = $commandBus;
        $this->productRepository = $productRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('setono:sylius-bulk-specials:assign')
            ->addArgument(
                'identifier',
                InputArgument::OPTIONAL,
                'Product identifier (id or code)'
            )
            ->setDescription('Assigns eligible specials to one or all products')
        ;
    }

    /**
     * @throws StringsException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = $input->getArgument('identifier');
        if (null === $identifier) {
            $products = $this->productRepository->findAll();
        } else {
            $products = $this->productRepository->findBy([
                is_numeric($identifier) ? 'id' : 'code' => $identifier,
            ]);
        }

        if (count($products) === 0) {
            $output->writeln('<error>No products found</error>');

            return 0;
        }

        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $this->commandBus->dispatch(new AssignEligibleSpecials($product));

            $output->writeln(sprintf(
                "<info>Eligible specials was assigned to product '%s'</info>",
                (string) $product
            ));
        }

        $output->writeln('Done');

        return 0;
    }
}
