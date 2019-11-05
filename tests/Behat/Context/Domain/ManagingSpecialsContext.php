<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkDiscountPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Setono\SyliusBulkDiscountPlugin\Model\DiscountInterface;
use Setono\SyliusBulkDiscountPlugin\Repository\DiscountRepositoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class ManagingSpecialsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var DiscountRepositoryInterface
     */
    private $discountRepository;

    public function __construct(SharedStorageInterface $sharedStorage, DiscountRepositoryInterface $specialRepository) {
        $this->sharedStorage = $sharedStorage;
        $this->discountRepository = $specialRepository;
    }

    /**
     * @When /^I delete a ("([^"]+)" special)$/
     */
    public function iDeleteSpecial(DiscountInterface $special)
    {
        $this->discountRepository->remove($special);
    }

    /**
     * @When /^I try to delete a ("([^"]+)" special)$/
     */
    public function iTryToDeleteSpecial(DiscountInterface $special)
    {
        try {
            $this->discountRepository->remove($special);
        } catch (ForeignKeyConstraintViolationException $exception) {
            $this->sharedStorage->set('last_exception', $exception);
        }
    }

    /**
     * @Then /^(this special) should no longer exist in the special registry$/
     */
    public function specialShouldNotExistInTheRegistry(DiscountInterface $special)
    {
        Assert::null($this->discountRepository->findOneBy(['code' => $special->getCode()]));
    }

    /**
     * @Then special :special should still exist in the registry
     */
    public function specialShouldStillExistInTheRegistry(DiscountInterface $special)
    {
        Assert::notNull($this->discountRepository->find($special->getId()));
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        Assert::isInstanceOf($this->sharedStorage->get('last_exception'), ForeignKeyConstraintViolationException::class);
    }
}
