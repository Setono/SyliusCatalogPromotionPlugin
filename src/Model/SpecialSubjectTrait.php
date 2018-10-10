<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductTaxon;

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

//    /**
//     * {@inheritdoc}
//     */
//    public function inTaxonCodes(array $taxonCodes): bool
//    {
//        foreach ($taxonCodes as $taxonCode) {
//            if ($this->inTaxonCode($taxonCode)) {
//                return true;
//            }
//        }
//        return false;
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function inTaxonCode(array $taxonCode): bool
//    {
//        /** ProductInterface $this */
//        if (null !== $this->getMainTaxon() && $this->getMainTaxon()->getCode() == $taxonCode) {
//            return true;
//        }
//
//        return in_array($taxonCode, $this->getProductTaxons()->map(function(ProductTaxon $productTaxon){
//            return $productTaxon->getTaxon()->getCode();
//        })->toArray());
//    }

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
        return $this->getSortedSpecials()->filter(function (Special $special) {
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
        return $this->getSortedSpecials()->filter(function (Special $special) use ($channelCode) {
            $specialsChannelCodes = $special->getChannels()->map(function (ChannelInterface $channel) {
                return $channel->getCode();
            })->toArray();

            return in_array($channelCode, $specialsChannelCodes) && $special->isEnabled();
        });
    }

    /**
     * @return ArrayCollection|Special[]
     */
    protected function getSortedSpecials(): Collection
    {
        $criteria = Criteria::create()->orderBy([
            'priority' => Criteria::DESC,
        ]);

        return $this->specials->matching($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecials(): Collection
    {
        return $this->specials;
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
    public function removeSpecials(): void
    {
        $this->specials->clear();
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
}
