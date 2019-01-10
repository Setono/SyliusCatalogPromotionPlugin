<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkSpecialsPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\SpecialRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class ManagingSpecialsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var SpecialRepositoryInterface
     */
    private $specialRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param SpecialRepositoryInterface $specialRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        SpecialRepositoryInterface $specialRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->specialRepository = $specialRepository;
    }

    /**
     * @When /^I delete a ("([^"]+)" special)$/
     */
    public function iDeleteSpecial(SpecialInterface $special)
    {
        $this->specialRepository->remove($special);
    }

    /**
     * @When /^I try to delete a ("([^"]+)" special)$/
     */
    public function iTryToDeleteSpecial(SpecialInterface $special)
    {
        try {
            $this->specialRepository->remove($special);
        } catch (ForeignKeyConstraintViolationException $exception) {
            $this->sharedStorage->set('last_exception', $exception);
        }
    }

    /**
     * @Then /^(this special) should no longer exist in the special registry$/
     */
    public function specialShouldNotExistInTheRegistry(SpecialInterface $special)
    {
        Assert::null($this->specialRepository->findOneBy(['code' => $special->getCode()]));
    }

    /**
     * @Then special :special should still exist in the registry
     */
    public function specialShouldStillExistInTheRegistry(SpecialInterface $special)
    {
        Assert::notNull($this->specialRepository->find($special->getId()));
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        Assert::isInstanceOf($this->sharedStorage->get('last_exception'), ForeignKeyConstraintViolationException::class);
    }
}
