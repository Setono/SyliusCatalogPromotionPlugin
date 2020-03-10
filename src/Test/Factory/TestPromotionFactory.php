<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Test\Factory;

use Safe\DateTime;
use Setono\SyliusCatalogPromotionPlugin\Model\PromotionInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class TestPromotionFactory implements TestPromotionFactoryInterface
{
    /** @var FactoryInterface */
    private $promotionFactory;

    public function __construct(FactoryInterface $promotionFactory)
    {
        $this->promotionFactory = $promotionFactory;
    }

    public function create(string $name): PromotionInterface
    {
        /** @var PromotionInterface $promotion */
        $promotion = $this->promotionFactory->createNew();

        $promotion->setName($name);
        $promotion->setCode(StringInflector::nameToLowercaseCode($name));
        $promotion->setStartsAt(new DateTime('-3 days'));
        $promotion->setEndsAt(new DateTime('+3 days'));

        return $promotion;
    }

    public function createForChannel(string $name, ChannelInterface $channel): PromotionInterface
    {
        $promotion = $this->create($name);
        $promotion->addChannel($channel);

        return $promotion;
    }
}
