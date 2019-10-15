<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Message\Handler;

use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\ProductRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\SpecialRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Handler\ProductRecalculateHandlerInterface;
use Setono\SyliusBulkSpecialsPlugin\Message\Command\AssignEligibleSpecials;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Setono\SyliusBulkSpecialsPlugin\Special\Checker\Eligibility\SpecialEligibilityCheckerInterface;

final class AssignEligibleSpecialsHandler extends AbstractHandler
{
    /** @var SpecialRepositoryInterface */
    private $specialRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var SpecialEligibilityCheckerInterface */
    private $specialEligibilityChecker;

    /** @var ProductRecalculateHandlerInterface */
    private $productRecalculateHandler;

    public function __construct(
        SpecialRepositoryInterface $specialRepository,
        ProductRepositoryInterface $productRepository,
        SpecialEligibilityCheckerInterface $specialEligibilityChecker,
        ProductRecalculateHandlerInterface $productRecalculateHandler
    ) {
        $this->specialRepository = $specialRepository;
        $this->productRepository = $productRepository;
        $this->specialEligibilityChecker = $specialEligibilityChecker;
        $this->productRecalculateHandler = $productRecalculateHandler;

        parent::__construct();
    }

    /**
     * @throws StringsException
     */
    public function __invoke(AssignEligibleSpecials $message): void
    {
        /** @var ProductInterface|null $product */
        $product = $this->productRepository->find($message->getProductId());

        if (null === $product) {
            return;
        }

        $this->logger->info(sprintf('Assigning eligible specials to product "%s"...', $product->getCode()));

        /** @var SpecialInterface[] $specials */
        $specials = $this->specialRepository->findAll();

        $product->removeSpecials();

        foreach ($specials as $special) {
            if ($this->specialEligibilityChecker->isEligible($product, $special)) {
                $this->logger->info(sprintf(
                    'Special "%s" is eligible for product "%s". Adding...',
                    $special->getCode(),
                    $product->getCode()
                ));

                $product->addSpecial($special);
            }
        }

        $this->productRepository->add($product);
        $this->productRecalculateHandler->handle($product);

        $this->logger->info(sprintf(
            "Product '%s' specials reassign finished.",
            $product->getCode()
        ));
    }
}
