<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Setono\SyliusCatalogPromotionPlugin\Model\PromotionInterface;
use Setono\SyliusCatalogPromotionPlugin\Repository\PromotionRepositoryInterface;
use Webmozart\Assert\Assert;

final class CatalogPromotionContext implements Context
{
    /** @var PromotionRepositoryInterface */
    private $promotionRepository;

    public function __construct(PromotionRepositoryInterface $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    /**
     * @Transform /^catalog promotion "([^"]+)"$/
     * @Transform /^"([^"]+)" catalog promotion/
     * @Transform :catalogPromotion
     */
    public function getCatalogPromotionByName($name): PromotionInterface
    {
        /** @var PromotionInterface $promotion */
        $promotion = $this->promotionRepository->findOneBy(['name' => $name]);

        Assert::notNull($promotion, sprintf('Catalog promotion with name "%s" does not exist', $name));

        return $promotion;
    }
}
