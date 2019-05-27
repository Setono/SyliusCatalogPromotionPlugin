<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface SpecialRuleInterface extends ResourceInterface, ConfigurableSpecialElementInterface
{
    public function setType(string $type): void;

    public function setConfiguration(array $configuration): void;

    public function setSpecial(?SpecialInterface $special): void;
}
