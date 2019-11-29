<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionsPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Setono\SyliusCatalogPromotionsPlugin\Model\PromotionInterface;
use Setono\SyliusCatalogPromotionsPlugin\Repository\PromotionRepositoryInterface;
use Webmozart\Assert\Assert;

final class DiscountContext implements Context
{
    /** @var PromotionRepositoryInterface */
    private $discountRepository;

    public function __construct(PromotionRepositoryInterface $discountRepository)
    {
        $this->discountRepository = $discountRepository;
    }

    /**
     * @Transform /^discount "([^"]+)"$/
     * @Transform /^"([^"]+)" discount/
     * @Transform :discount
     */
    public function getDiscountByName($discountName): PromotionInterface
    {
        /** @var PromotionInterface $discount */
        $discount = $this->discountRepository->findOneBy(['name' => $discountName]);

        Assert::notNull($discount, sprintf('Discount with name "%s" does not exist', $discountName));

        return $discount;
    }
}
