<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Test\Factory;

use DateTime;
use Setono\SyliusBulkDiscountPlugin\Model\DiscountInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class TestDiscountFactory implements TestDiscountFactoryInterface
{
    /** @var FactoryInterface */
    private $discountFactory;

    public function __construct(FactoryInterface $discountFactory)
    {
        $this->discountFactory = $discountFactory;
    }

    public function create(string $name): DiscountInterface
    {
        /** @var DiscountInterface $discount */
        $discount = $this->discountFactory->createNew();

        $discount->setName($name);
        $discount->setCode(StringInflector::nameToLowercaseCode($name));
        $discount->setStartsAt(new DateTime('-3 days'));
        $discount->setEndsAt(new DateTime('+3 days'));

        return $discount;
    }

    public function createForChannel(string $name, ChannelInterface $channel): DiscountInterface
    {
        $discount = $this->create($name);
        $discount->addChannel($channel);

        return $discount;
    }
}
