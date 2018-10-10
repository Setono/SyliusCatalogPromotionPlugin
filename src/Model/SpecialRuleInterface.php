<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Interface SpecialRuleInterface
 */
interface SpecialRuleInterface extends ResourceInterface, ConfigurableSpecialElementInterface
{
    /**
     * @param string $type
     */
    public function setType(string $type): void;

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration): void;

    /**
     * @param SpecialInterface|null $special
     */
    public function setSpecial(?SpecialInterface $special): void;
}
