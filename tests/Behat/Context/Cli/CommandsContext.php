<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkSpecialsPlugin\Behat\Context\Cli;

use Behat\Behat\Context\Context;
use Setono\SyliusBulkSpecialsPlugin\Command\CheckActiveCommand;
use Setono\SyliusBulkSpecialsPlugin\Command\CommandInterface;
use Setono\SyliusBulkSpecialsPlugin\Command\ReassignCommand;
use Setono\SyliusBulkSpecialsPlugin\Command\RecalculateProductCommand;
use Setono\SyliusBulkSpecialsPlugin\Command\RecalculateSpecialCommand;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Sylius\Behat\Service\SharedStorage;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class CommandsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var CommandTester
     */
    private $tester;

    /**
     * @var CheckActiveCommand
     */
    private $command;

    /**
     * @var CheckActiveCommand
     */
    private $checkActiveCommand;

    /**
     * @var ReassignCommand
     */
    private $reassignCommand;

    /**
     * @var RecalculateProductCommand
     */
    private $recalculateProductCommand;

    /**
     * @var RecalculateSpecialCommand
     */
    private $recalculateSpecialCommand;

    /**
     * CommandsContext constructor.
     * @param SharedStorage $sharedStorage
     * @param KernelInterface $kernel
     * @param CheckActiveCommand $checkActiveCommand
     * @param ReassignCommand $reassignCommand
     * @param RecalculateProductCommand $recalculateProductCommand
     * @param RecalculateSpecialCommand $recalculateSpecialCommand
     */
    public function __construct(
        SharedStorage $sharedStorage,
        KernelInterface $kernel,
        CheckActiveCommand $checkActiveCommand,
        ReassignCommand $reassignCommand,
        RecalculateProductCommand $recalculateProductCommand,
        RecalculateSpecialCommand $recalculateSpecialCommand
    ) {
        $this->sharedStorage = $sharedStorage;

        $this->kernel = $kernel;
        $this->checkActiveCommand = $checkActiveCommand;
        $this->reassignCommand = $reassignCommand;
        $this->recalculateProductCommand = $recalculateProductCommand;
        $this->recalculateSpecialCommand = $recalculateSpecialCommand;
    }

    /**
     * @When I run check active CLI command
     */
    public function iRunCheckActiveCommand(): void
    {
        $this->executeCommand(
            $this->checkActiveCommand
        );
    }

    /**
     * @Given specials was reassigned
     * @When I reassign specials
     * @When I run reassign CLI command
     */
    public function iRunReassignCommand(): void
    {
        $this->executeCommand(
            $this->reassignCommand
        );
    }

    /**
     * @Given products prices was recalculated based on specials
     * @When I recalculate special price for product :product
     * @When I recalculate special prices for all products
     */
    public function iRunRecalculateProductCommand(?ProductInterface $product = null): void
    {
        $this->executeCommand(
            $this->recalculateProductCommand,
            [
                'identifier' => $product ? $product->getId() : null
            ]
        );

        if ($product) {
            $this->sharedStorage->set('product', $product);
        }
    }

    /**
     * @When I recalculate prices of products related to special :special
     * @When I recalculate prices of products related to this special
     */
    public function iRunRecalculateSpecialCommand(?SpecialInterface $special = null): void
    {
        if (null === $special) {
            $special = $this->sharedStorage->get('special');
        }

        $this->executeCommand(
            $this->recalculateSpecialCommand,
            [
                'identifier' => $special->getId()
            ]
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

    /**
     * @param array $parameters
     */
    private function executeCommand(CommandInterface $command, array $parameters = [])
    {
        $this->command = $command;
        $this->application = new Application($this->kernel);
        $this->application->add(
            $this->command
        );
        $this->tester = new CommandTester($this->command);
        $this->tester->execute([
            'command' => $this->command->getName()
        ] + $parameters);
    }
}
