<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkDiscountPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Setono\SyliusBulkDiscountPlugin\Model\DiscountInterface;
use Setono\SyliusBulkDiscountPlugin\Repository\DiscountRepositoryInterface;
use Webmozart\Assert\Assert;

final class DiscountContext implements Context
{
    /**
     * @var DiscountRepositoryInterface
     */
    private $discountRepository;

    public function __construct(DiscountRepositoryInterface $discountRepository) {
        $this->discountRepository = $discountRepository;
    }

    /**
     * @Transform /^discount "([^"]+)"$/
     * @Transform /^"([^"]+)" discount/
     * @Transform :discount
     */
    public function getDiscountByName($discountName): DiscountInterface
    {
        /** @var DiscountInterface $discount */
        $discount = $this->discountRepository->findOneBy(['name' => $discountName]);

        Assert::notNull($discount, sprintf('Discount with name "%s" does not exist', $discountName));

        return $discount;
    }
}
