<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionsPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Setono\SyliusCatalogPromotionsPlugin\Model\PromotionInterface;
use Setono\SyliusCatalogPromotionsPlugin\Repository\PromotionRepositoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class ManagingSpecialsContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var PromotionRepositoryInterface */
    private $discountRepository;

    public function __construct(SharedStorageInterface $sharedStorage, PromotionRepositoryInterface $specialRepository)
    {
        $this->sharedStorage = $sharedStorage;
        $this->discountRepository = $specialRepository;
    }

    /**
     * @When /^I delete a ("([^"]+)" special)$/
     */
    public function iDeleteSpecial(PromotionInterface $special)
    {
        $this->discountRepository->remove($special);
    }

    /**
     * @When /^I try to delete a ("([^"]+)" special)$/
     */
    public function iTryToDeleteSpecial(PromotionInterface $special)
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
    public function specialShouldNotExistInTheRegistry(PromotionInterface $special)
    {
        Assert::null($this->discountRepository->findOneBy(['code' => $special->getCode()]));
    }

    /**
     * @Then special :special should still exist in the registry
     */
    public function specialShouldStillExistInTheRegistry(PromotionInterface $special)
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
