<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\SpecialRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\Special;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Setono\SyliusBulkSpecialsPlugin\Special\Checker\Eligibility\SpecialEligibilityCheckerInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository;

/**
 * Class ProductRecalculateHandler
 */
class EligibleSpecialsReassignHandler implements EligibleSpecialsReassignHandlerInterface
{
    /**
     * @var SpecialRepositoryInterface
     */
    private $specialRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var SpecialEligibilityCheckerInterface
     */
    private $specialEligibilityChecker;

    /**
     * @var ProductRecalculateHandlerInterface
     */
    private $productRecalculateHandler;

    /**
     * EligibleSpecialsReassignHandler constructor.
     * @param SpecialRepositoryInterface $specialRepository
     * @param ProductRepository $productRepository
     * @param SpecialEligibilityCheckerInterface $specialEligibilityChecker
     * @param ProductRecalculateHandlerInterface $productRecalculateHandler
     */
    public function __construct(
        SpecialRepositoryInterface $specialRepository,
        ProductRepository $productRepository,
        SpecialEligibilityCheckerInterface $specialEligibilityChecker,
        ProductRecalculateHandlerInterface $productRecalculateHandler
    ) {
        $this->specialRepository = $specialRepository;
        $this->productRepository = $productRepository;
        $this->specialEligibilityChecker = $specialEligibilityChecker;
        $this->productRecalculateHandler = $productRecalculateHandler;
    }

    /**
     * @param SpecialSubjectInterface $subject
     */
    public function handle(SpecialSubjectInterface $subject): void
    {
        $specials = $this->specialRepository->findAll();

        $subject->removeSpecials();

        /** @var Special $special */
        foreach ($specials as $special) {
            if ($this->specialEligibilityChecker->isEligible($subject, $special)) {
                $subject->addSpecial($special);
            }
        }

        $this->productRepository->add($subject);
        $this->productRecalculateHandler->handle($subject);
    }
}
