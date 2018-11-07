<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Command;

use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\SpecialRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Handler\SpecialRecalculateHandlerInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RecalculateSpecialCommand
 */
class RecalculateSpecialCommand extends Command
{
    /**
     * @var SpecialRepositoryInterface
     */
    protected $specialRepository;

    /**
     * @var SpecialRecalculateHandlerInterface
     */
    protected $specialRecalculateHandler;

    /**
     * RecalculateSpecialCommand constructor.
     *
     * @param SpecialRepositoryInterface $specialRepository
     * @param SpecialRecalculateHandlerInterface $specialRecalculateHandler
     */
    public function __construct(
        SpecialRepositoryInterface $specialRepository,
        SpecialRecalculateHandlerInterface $specialRecalculateHandler
    ) {
        $this->specialRepository = $specialRepository;
        $this->specialRecalculateHandler = $specialRecalculateHandler;

        parent::__construct(null);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('setono:sylius-bulk-specials:recalculate-special')
            ->addArgument(
                'identifier',
                InputArgument::REQUIRED,
                'Special identifier (ID or code)'
            )
            ->setDescription('Recalculate given Special-related Products')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $identifier = $input->getArgument('identifier');
        $special = $this->specialRepository->findOneBy([
            is_numeric($identifier) ? 'id' : 'code' => $identifier,
        ]);

        if (!$special instanceof SpecialInterface) {
            $output->writeln(sprintf(
                "<error>Special with identifier '%s' was not found</error>",
                $identifier
            ));

            return;
        }

        $this->specialRecalculateHandler->handleSpecial($special);
        $output->writeln(sprintf(
            "<info>All Products related to Special '%s' was recalculated</info>",
            (string) $special
        ));

        $output->writeln('Done');
    }
}
