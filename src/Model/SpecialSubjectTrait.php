<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

trait SpecialSubjectTrait
{
    /** @var Collection|SpecialInterface[] */
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

        return $this->getSortedSpecials()->filter(function (SpecialInterface $special) use ($date) {
            return $special->isSpecialActiveAt($date);
        });
    }

    public function hasExclusiveSpecialsForChannelCode(string $channelCode): bool
    {
        return null !== $this->getFirstExclusiveSpecialForChannelCode($channelCode);
    }

    public function getFirstExclusiveSpecialForChannelCode(string $channelCode): ?SpecialInterface
    {
        return $this->getExclusiveSpecialsForChannelCode($channelCode)->first() ?: null;
    }

    /**
     * @return Collection|SpecialInterface[]
     */
    public function getExclusiveSpecialsForChannelCode(string $channelCode): Collection
    {
        return $this->getActiveSpecialsForChannelCode($channelCode)->filter(function (SpecialInterface $special) {
            return $special->isExclusive();
        });
    }

    /**
     * @return Collection|SpecialInterface[]
     */
    public function getActiveSpecialsForChannelCode(string $channelCode): Collection
    {
        $date = new \DateTime();

        return $this->getSortedSpecials()->filter(function (SpecialInterface $special) use ($channelCode, $date) {
            return \in_array($channelCode, $special->getChannelCodes(), true) && $special->isSpecialActiveAt($date);
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

    public function hasSpecial(SpecialInterface $special): bool
    {
        return $this->specials->contains($special);
    }

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

    public function removeSpecial(SpecialInterface $special): void
    {
        if ($this->hasSpecial($special)) {
            $this->specials->removeElement($special);
        }
    }
}
