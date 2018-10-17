<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Command;

use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\SpecialRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Handler\SpecialRecalculateHandlerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckActiveCommand
 */
class CheckActiveCommand extends Command implements CommandInterface
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
     * CheckActiveCommand constructor.
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
    protected function configure()
    {
        $this
            ->setName('setono:sylius-bulk-specials:check-active')
            ->setDescription('Find specials that not enabled but should be enabled (or vice versa), fix that and trigger recalculations')
            ->setHelp('This command should be scheduled via cron to run check every minute')
            ->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'Show what would have been recalculated')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dryRun = $input->getOption('dry-run');

        // Disabled Specials that should be enabled
        $specials = $this->specialRepository->findAccidentallyDisabled();
        foreach ($specials as $special) {
            $output->writeln(sprintf(
                "Special '%s' was accidentally disabled...",
                $special->getCode()
            ));

            if (!$dryRun) {
                $special->setEnabled(true);
                $this->specialRepository->add($special);
                $this->specialRecalculateHandler->handle($special);
            }

            $output->writeln(sprintf(
                "Special '%s' was enabled and recalculated (or queued to recalculate).",
                $special->getCode()
            ));
        }

        // Enabled Specials that should be disabled
        $specials = $this->specialRepository->findAccidentallyEnabled();
        foreach ($specials as $special) {
            $output->writeln(sprintf(
                "Special '%s' was accidentally enabled...",
                $special->getCode()
            ));

            if (!$dryRun) {
                $special->setEnabled(false);
                $this->specialRepository->add($special);
                $this->specialRecalculateHandler->handle($special);
            }

            $output->writeln(sprintf(
                "Special '%s' was disabled and recalculated (or queued to recalculate).",
                $special->getCode()
            ));
        }

        $output->writeln('Done');
    }
}
