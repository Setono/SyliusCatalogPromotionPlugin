<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionsPlugin\Behat\Context\Cli;

use Behat\Behat\Context\Context;
use Setono\SyliusCatalogPromotionsPlugin\Command\ProcessPromotionsCommand;
use Sylius\Behat\Service\SharedStorage;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class CommandsContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var KernelInterface */
    private $kernel;

    /** @var Application */
    private $application;

    /** @var CommandTester */
    private $tester;

    /** @var ProcessPromotionsCommand */
    private $processDiscountsCommand;

    /**
     * CommandsContext constructor.
     */
    public function __construct(
        SharedStorage $sharedStorage,
        KernelInterface $kernel,
        ProcessPromotionsCommand $processDiscountsCommand
    ) {
        $this->sharedStorage = $sharedStorage;

        $this->kernel = $kernel;
        $this->checkActiveCommand = $processDiscountsCommand;
    }

    /**
     * @When I run process promotions CLI command
     */
    public function iRunProcessDiscountsCommand(): void
    {
        $this->executeCommand(
            $this->processDiscountsCommand
        );
    }

    /**
     * @Then the command should finish successfully
     */
    public function commandSuccess(): void
    {
        Assert::same($this->tester->getStatusCode(), 0);
    }

    /**
     * @Then echo command output
     */
    public function echoCommandOutput(): void
    {
        echo $this->tester->getDisplay();
    }

    /**
     * @Then I should see output :text
     */
    public function iShouldSeeOutput(string $text): void
    {
        Assert::contains($this->tester->getDisplay(), $text);
    }

    /**
     * @Then Output shouldn't contain :text
     */
    public function iShouldNotSeeOutput(string $text): void
    {
        Assert::notContains($this->tester->getDisplay(), $text);
    }

    private function executeCommand(Command $command, array $parameters = [])
    {
        $this->processDiscountsCommand = $command;
        $this->application = new Application($this->kernel);
        $this->application->add(
            $this->processDiscountsCommand
        );
        $this->tester = new CommandTester($this->processDiscountsCommand);
        $this->tester->execute([
            'command' => $this->processDiscountsCommand->getName(),
        ] + $parameters);
    }
}
