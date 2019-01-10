<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkSpecialsPlugin\Behat\Page\Admin\Special;

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

    /**
     * @param string $name
     */
    public function nameIt($name);

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

    /**
     * @param \DateTimeInterface $dateTime
     */
    public function setStartsAt(\DateTimeInterface $dateTime);

    /**
     * @param \DateTimeInterface $dateTime
     */
    public function setEndsAt(\DateTimeInterface $dateTime);

    /**
     * @param \DateTimeInterface $dateTime
     * @return bool
     */
    public function hasStartsAt(\DateTimeInterface $dateTime): bool;

    /**
     * @param \DateTimeInterface $dateTime
     * @return bool
     */
    public function hasEndsAt(\DateTimeInterface $dateTime): bool;
}
