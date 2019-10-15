<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

class SpecialRule implements SpecialRuleInterface
{
    /** @var mixed */
    protected $id;

    /** @var string */
    protected $type;

    /** @var array */
    protected $configuration = [];

    /** @var SpecialInterface|null */
    protected $special;

    public function getId()
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

    public function getSpecial(): ?SpecialInterface
    {
        return $this->special;
    }

    public function setSpecial(?SpecialInterface $special): void
    {
        $this->special = $special;
    }
}
