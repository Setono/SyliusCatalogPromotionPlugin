<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

/**
 * Class SpecialRule
 */
class SpecialRule implements SpecialRuleInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var SpecialInterface
     */
    protected $special;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecial(): ?SpecialInterface
    {
        return $this->special;
    }

    /**
     * {@inheritdoc}
     */
    public function setSpecial(?SpecialInterface $special): void
    {
        $this->special = $special;
    }
}
