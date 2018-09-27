<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Setono\SyliusBulkSpecialsPlugin\Model\Special;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * Trait SpecialSubjectTrait
 */
trait SpecialSubjectTrait
{
    /**
     * @var Collection|SpecialInterface[]
     */
    protected $specials;

    /**
     * SpecialSubjectTrait constructor.
     */
    public function __construct()
    {
        $this->specials = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function hasExclusiveSpecials(): bool
    {
        return null !== $this->getFirstExclusiveSpecial();
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstExclusiveSpecial(): ?SpecialInterface
    {
        return $this->getExclusiveSpecials()->first() ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function getExclusiveSpecials(): Collection
    {
        return $this->getActiveSpecials()->filter(function (Special $special) {
            return $special->isExclusive();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveSpecials(): Collection
    {
        return $this->specials->filter(function (Special $special) {
            return $special->isEnabled();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function hasExclusiveSpecialsForChannelCode(string $channelCode): bool
    {
        return null !== $this->getFirstExclusiveSpecialForChannelCode($channelCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstExclusiveSpecialForChannelCode(string $channelCode): ?SpecialInterface
    {
        return $this->getExclusiveSpecialsForChannelCode($channelCode)->first() ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function getExclusiveSpecialsForChannelCode(string $channelCode): Collection
    {
        return $this->getActiveSpecialsForChannelCode($channelCode)->filter(function (Special $special) {
            return $special->isExclusive();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveSpecialsForChannelCode(string $channelCode): Collection
    {
        return $this->specials->filter(function (Special $special) use ($channelCode) {
            $specialsChannelCodes = $special->getChannels()->map(function (ChannelInterface $channel) {
                return $channel->getCode();
            });

            return in_array($channelCode, $specialsChannelCodes) && $special->isEnabled();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function hasSpecial(SpecialInterface $special): bool
    {
        return $this->specials->contains($special);
    }

    /**
     * {@inheritdoc}
     */
    public function addSpecial(SpecialInterface $special): void
    {
        if (!$this->hasSpecial($special)) {
            $this->specials->add($special);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeSpecial(SpecialInterface $special): void
    {
        if ($this->hasSpecial($special)) {
            $this->specials->removeElement($special);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecials(): Collection
    {
        return $this->specials;
    }
}
