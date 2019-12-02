<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Model;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Component\Resource\Model\TimestampableTrait;

trait ChannelPricingTrait
{
    use TimestampableTrait;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     *
     * @var bool
     */
    protected $manuallyDiscounted = false;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=4, options={"default": 1})
     *
     * @var float
     */
    protected $multiplier = 1;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     *
     * @var DateTimeInterface|null
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     *
     * @var DateTimeInterface|null
     */
    protected $updatedAt;

    public function isManuallyDiscounted(): bool
    {
        return $this->manuallyDiscounted;
    }

    public function setManuallyDiscounted(bool $manuallyDiscounted): void
    {
        $this->manuallyDiscounted = $manuallyDiscounted;
    }

    public function getMultiplier(): float
    {
        return $this->multiplier;
    }
}
