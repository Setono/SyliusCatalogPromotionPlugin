<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface SpecialRuleInterface extends ResourceInterface, ConfigurableSpecialElementInterface
{
    /**
     * @param string|null $type
     */
    public function setType(?string $type): void;

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration): void;

    /**
     * @param SpecialInterface|null $promotion
     */
    public function setSpecial(?SpecialInterface $promotion): void;
}
