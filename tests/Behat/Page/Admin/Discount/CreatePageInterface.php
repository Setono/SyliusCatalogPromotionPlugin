<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionPlugin\Behat\Page\Admin\Discount;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $actionPercent
     */
    public function specifyActionPercent($actionPercent);

    /**
     * @param string $actionType
     */
    public function specifyActionType($actionType);

    public function specifyCode(string $code): void;

    public function nameIt(string $name): void;

    /**
     * @param string $ruleName
     */
    public function addRule($ruleName);

    /**
     * @param string $option
     * @param string $value
     * @param bool $multiple
     */
    public function selectRuleOption($option, $value, $multiple = false);

    /**
     * @param string $option
     * @param string|string[] $value
     * @param bool $multiple
     */
    public function selectAutocompleteRuleOption($option, $value, $multiple = false);

    /**
     * @param string $option
     * @param string $value
     */
    public function fillRuleOption($option, $value);

    public function makeExclusive();

    /**
     * @param string $name
     */
    public function checkChannel($name);

    public function setStartsAt(\DateTimeInterface $dateTime);

    public function setEndsAt(\DateTimeInterface $dateTime);
}
