<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Psr\Log\LoggerInterface;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\SpecialRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\Special;
use Setono\SyliusBulkSpecialsPlugin\Special\Checker\Eligibility\SpecialEligibilityCheckerInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository;

class EligibleSpecialsReassignHandler extends AbstractProductHandler implements EligibleSpecialsReassignHandlerInterface
{
    /** @var SpecialRepositoryInterface */
    private $specialRepository;

    /** @var ProductRepository */
    private $productRepository;

    /** @var SpecialEligibilityCheckerInterface */
    private $specialEligibilityChecker;

    /** @var ProductRecalculateHandlerInterface */
    private $productRecalculateHandler;

    public function __construct(
        LoggerInterface $logger,
        SpecialRepositoryInterface $specialRepository,
        ProductRepository $productRepository,
        SpecialEligibilityCheckerInterface $specialEligibilityChecker,
        ProductRecalculateHandlerInterface $productRecalculateHandler
    ) {
        parent::__construct($logger);

        $this->specialRepository = $specialRepository;
        $this->productRepository = $productRepository;
        $this->specialEligibilityChecker = $specialEligibilityChecker;
        $this->productRecalculateHandler = $productRecalculateHandler;
    }

    /**
     * @throws StringsException
     */
    public function handleProduct(ProductInterface $product): void
    {
        $this->log(sprintf(
            "Product '%s' specials reassign started...",
            $product->getCode()
        ));

        $specials = $this->specialRepository->findAll();

        $product->removeSpecials();

        /** @var Special $special */
        foreach ($specials as $special) {
            if ($this->specialEligibilityChecker->isEligible($product, $special)) {
                $this->log(sprintf(
                    "Special '%s' is eligible for product '%s'. Adding...",
                    $special->getCode(),
                    $product->getCode()
                ));

                $product->addSpecial($special);
            }
        }

        $this->productRepository->add($product);
        $this->productRecalculateHandler->handle($product);

        $this->log(sprintf(
            "Product '%s' specials reassign finished.",
            $product->getCode()
        ));
    }
}
