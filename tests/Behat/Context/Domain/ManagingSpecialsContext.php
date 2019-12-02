<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Setono\SyliusCatalogPromotionPlugin\Model\PromotionInterface;
use Setono\SyliusCatalogPromotionPlugin\Repository\PromotionRepositoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class ManagingSpecialsContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var PromotionRepositoryInterface */
    private $promotionRepository;

    public function __construct(SharedStorageInterface $sharedStorage, PromotionRepositoryInterface $specialRepository)
    {
        $this->sharedStorage = $sharedStorage;
        $this->promotionRepository = $specialRepository;
    }

    /**
     * @When /^I delete a ("([^"]+)" special)$/
     */
    public function iDeleteSpecial(PromotionInterface $special)
    {
        $this->promotionRepository->remove($special);
    }

    /**
     * @When /^I try to delete a ("([^"]+)" special)$/
     */
    public function iTryToDeleteSpecial(PromotionInterface $special)
    {
        try {
            $this->promotionRepository->remove($special);
        } catch (ForeignKeyConstraintViolationException $exception) {
            $this->sharedStorage->set('last_exception', $exception);
        }
    }

    /**
     * @Then /^(this special) should no longer exist in the special registry$/
     */
    public function specialShouldNotExistInTheRegistry(PromotionInterface $special)
    {
        Assert::null($this->promotionRepository->findOneBy(['code' => $special->getCode()]));
    }

    /**
     * @Then special :special should still exist in the registry
     */
    public function specialShouldStillExistInTheRegistry(PromotionInterface $special)
    {
        Assert::notNull($this->promotionRepository->find($special->getId()));
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        Assert::isInstanceOf($this->sharedStorage->get('last_exception'), ForeignKeyConstraintViolationException::class);
    }
}
