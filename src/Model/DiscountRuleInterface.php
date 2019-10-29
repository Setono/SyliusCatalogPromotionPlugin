<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface DiscountRuleInterface extends ResourceInterface, ConfigurableDiscountElementInterface
{
    public function getId(): ?int;

    public function setType(string $type): void;

    public function setConfiguration(array $configuration): void;

    public function setDiscount(?DiscountInterface $discount): void;
}
