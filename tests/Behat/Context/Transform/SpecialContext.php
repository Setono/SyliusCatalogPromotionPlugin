<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkSpecialsPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\SpecialRepositoryInterface;
use Webmozart\Assert\Assert;

final class SpecialContext implements Context
{
    /**
     * @var SpecialRepositoryInterface
     */
    private $specialRepository;

    /**
     * SpecialContext constructor.
     */
    public function __construct(
        SpecialRepositoryInterface $specialRepository
    ) {
        $this->specialRepository = $specialRepository;
    }

    /**
     * @Transform /^special "([^"]+)"$/
     * @Transform /^"([^"]+)" special$/
     * @Transform :special
     */
    public function getSpecialByName($specialName)
    {
        $special = $this->specialRepository->findOneBy(['name' => $specialName]);

        Assert::notNull(
            $special,
            sprintf('Special with name "%s" does not exist', $specialName)
        );

        return $special;
    }
}
