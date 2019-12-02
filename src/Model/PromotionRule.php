<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Model;

class PromotionRule implements PromotionRuleInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $type;

    /** @var array */
    protected $configuration = [];

    /** @var PromotionInterface|null */
    protected $promotion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function getPromotion(): ?PromotionInterface
    {
        return $this->promotion;
    }

    public function setPromotion(?PromotionInterface $promotion): void
    {
        $this->promotion = $promotion;
    }
}
