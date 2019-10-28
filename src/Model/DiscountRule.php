<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Model;

class DiscountRule implements DiscountRuleInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $type;

    /** @var array */
    protected $configuration = [];

    /** @var DiscountInterface|null */
    protected $discount;

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

    public function getDiscount(): ?DiscountInterface
    {
        return $this->discount;
    }

    public function setDiscount(?DiscountInterface $discount): void
    {
        $this->discount = $discount;
    }
}
