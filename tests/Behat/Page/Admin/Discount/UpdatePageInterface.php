<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionsPlugin\Behat\Page\Admin\Discount;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @param int|null $priority
     */
    public function setPriority($priority);

    /**
     * @return int
     */
    public function getPriority();

    public function nameIt(string $name): void;

    /**
     * @param string $channelName
     *
     * @return bool
     */
    public function checkChannelsState($channelName);

    /**
     * @return bool
     */
    public function isCodeDisabled();

    public function makeExclusive();

    /**
     * @param string $name
     */
    public function checkChannel($name);

    public function setStartsAt(\DateTimeInterface $dateTime);

    public function setEndsAt(\DateTimeInterface $dateTime);

    public function hasStartsAt(\DateTimeInterface $dateTime): bool;

    public function hasEndsAt(\DateTimeInterface $dateTime): bool;
}
