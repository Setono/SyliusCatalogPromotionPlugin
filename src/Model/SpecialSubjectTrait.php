<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Sylius\Component\Core\Model\ChannelInterface;

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
     * @return Collection|SpecialInterface[]
     */
    public function getActiveSpecials(): Collection
    {
        $date = new \DateTime();
        return $this->getSortedSpecials()->filter(function (SpecialInterface $special) use($date) {
            return $special->isSpecialActiveAt($date);
        });
    }

    /**
     * @param string $channelCode
     *
     * @return bool
     */
    public function hasExclusiveSpecialsForChannelCode(string $channelCode): bool
    {
        return null !== $this->getFirstExclusiveSpecialForChannelCode($channelCode);
    }

    /**
     * @param string $channelCode
     *
     * @return SpecialInterface|null
     */
    public function getFirstExclusiveSpecialForChannelCode(string $channelCode): ?SpecialInterface
    {
        return $this->getExclusiveSpecialsForChannelCode($channelCode)->first() ?: null;
    }

    /**
     * @param string $channelCode
     *
     * @return Collection|SpecialInterface[]
     */
    public function getExclusiveSpecialsForChannelCode(string $channelCode): Collection
    {
        return $this->getActiveSpecialsForChannelCode($channelCode)->filter(function (SpecialInterface $special) {
            return $special->isExclusive();
        });
    }

    /**
     * @param string $channelCode
     *
     * @return Collection|SpecialInterface[]
     */
    public function getActiveSpecialsForChannelCode(string $channelCode): Collection
    {
        $date = new \DateTime();
        return $this->getSortedSpecials()->filter(function (SpecialInterface $special) use ($channelCode, $date) {
            $specialsChannelCodes = $special->getChannels()->map(function (ChannelInterface $channel) {
                return $channel->getCode();
            })->toArray();

            return \in_array($channelCode, $specialsChannelCodes, true) && $special->isSpecialActiveAt($date);
        });
    }

    /**
     * @return Collection|SpecialInterface[]
     */
    protected function getSortedSpecials(): Collection
    {
        $criteria = Criteria::create()->orderBy([
            'priority' => Criteria::DESC,
        ]);

        return $this->specials->matching($criteria);
    }

    /**
     * @return Collection|SpecialInterface[]
     */
    public function getSpecials(): Collection
    {
        return $this->specials;
    }

    /**
     * @param SpecialInterface $special
     *
     * @return bool
     */
    public function hasSpecial(SpecialInterface $special): bool
    {
        return $this->specials->contains($special);
    }

    /**
     * @param SpecialInterface $special
     */
    public function addSpecial(SpecialInterface $special): void
    {
        if (!$this->hasSpecial($special)) {
            $this->specials->add($special);
        }
    }

    public function removeSpecials(): void
    {
        $this->specials->clear();
    }

    /**
     * @param SpecialInterface $special
     */
    public function removeSpecial(SpecialInterface $special): void
    {
        if ($this->hasSpecial($special)) {
            $this->specials->removeElement($special);
        }
    }
}
