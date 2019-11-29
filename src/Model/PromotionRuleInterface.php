<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionsPlugin\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface PromotionRuleInterface extends ResourceInterface, ConfigurablePromotionElementInterface
{
    public function getId(): ?int;

    public function setType(string $type): void;

    public function setConfiguration(array $configuration): void;

    public function setPromotion(?PromotionInterface $promotion): void;
}
