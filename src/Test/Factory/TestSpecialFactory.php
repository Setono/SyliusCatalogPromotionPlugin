<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Test\Factory;

use DateTime;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class TestSpecialFactory implements TestSpecialFactoryInterface
{
    /** @var FactoryInterface */
    private $specialFactory;

    public function __construct(FactoryInterface $specialFactory)
    {
        $this->specialFactory = $specialFactory;
    }

    public function create(string $name): SpecialInterface
    {
        /** @var SpecialInterface $special */
        $special = $this->specialFactory->createNew();

        $special->setName($name);
        $special->setCode(StringInflector::nameToLowercaseCode($name));
        $special->setStartsAt(new DateTime('-3 days'));
        $special->setEndsAt(new DateTime('+3 days'));

        return $special;
    }

    public function createForChannel(string $name, ChannelInterface $channel): SpecialInterface
    {
        $special = $this->create($name);
        $special->addChannel($channel);

        return $special;
    }
}
