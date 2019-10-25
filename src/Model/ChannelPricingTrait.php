<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait ChannelPricingTrait
{
    /**
     * @ORM\Column(type="decimal", precision=8, scale=4, options={"default": 1})
     *
     * @var float
     */
    protected $multiplier = 1;

    public function getMultiplier(): float
    {
        return $this->multiplier;
    }
}
