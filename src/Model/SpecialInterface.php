<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Interface SpecialInterface
 */
interface SpecialInterface extends ChannelsAwareInterface, CodeAwareInterface, TimestampableInterface, ResourceInterface
{
    /**
     * @return string
     */
    public function __toString();

    /**
     * @return float
     */
    public function getMultiplier(): float;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void;

    /**
     * @return int
     */
    public function getPriority(): int;

    /**
     * @param int|null $priority
     */
    public function setPriority(?int $priority): void;

    /**
     * @return bool
     */
    public function isExclusive(): bool;

    /**
     * @param bool $exclusive
     */
    public function setExclusive(bool $exclusive): void;

    /**
     * @return \DateTimeInterface|null
     */
    public function getStartsAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface|null $startsAt
     */
    public function setStartsAt(?\DateTimeInterface $startsAt): void;

    /**
     * @return \DateTimeInterface|null
     */
    public function getEndsAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface|null $endsAt
     */
    public function setEndsAt(?\DateTimeInterface $endsAt): void;

    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void;

    /**
     * @return Collection|SpecialRuleInterface[]
     */
    public function getRules(): Collection;

    /**
     * @return bool
     */
    public function hasRules(): bool;

    /**
     * @param SpecialRuleInterface $rule
     *
     * @return bool
     */
    public function hasRule(SpecialRuleInterface $rule): bool;

    /**
     * @param SpecialRuleInterface $rule
     */
    public function addRule(SpecialRuleInterface $rule): void;

    /**
     * @param SpecialRuleInterface $rule
     */
    public function removeRule(SpecialRuleInterface $rule): void;

    /**
     * @return string
     */
    public function getActionType(): string;

    /**
     * @param string $actionType
     */
    public function setActionType(string $actionType): void;

    /**
     * @return float
     */
    public function getActionPercent(): float;

    /**
     * @param float $actionPercent
     */
    public function setActionPercent(float $actionPercent): void;
}
