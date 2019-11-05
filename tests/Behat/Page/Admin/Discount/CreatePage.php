<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkDiscountPlugin\Behat\Page\Admin\Discount;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Service\AutocompleteHelper;
use Tests\Setono\SyliusBulkDiscountPlugin\Behat\Behaviour\SpecifiesItsActionPercent;
use Tests\Setono\SyliusBulkDiscountPlugin\Behat\Behaviour\SpecifiesItsActionType;
use Webmozart\Assert\Assert;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use NamesIt;
    use SpecifiesItsCode;
    use SpecifiesItsActionType;
    use SpecifiesItsActionPercent;
    use PageDefinedElements;

    public function addRule($ruleName)
    {
        $count = count($this->getCollectionItems('rules'));

        $this->getDocument()->clickLink('Add rule');

        $this->getDocument()->waitFor(5, function () use ($count) {
            return $count + 1 === count($this->getCollectionItems('rules'));
        });

        $this->selectRuleOption('Type', $ruleName);
    }

    public function selectRuleOption($option, $value, $multiple = false)
    {
        $this->getLastCollectionItem('rules')->find('named', ['select', $option])->selectOption($value, $multiple);
    }

    public function selectAutocompleteRuleOption($option, $value, $multiple = false)
    {
        $option = strtolower(str_replace(' ', '_', $option));

        $ruleAutocomplete = $this
            ->getLastCollectionItem('rules')
            ->find('css', sprintf('input[type="hidden"][name*="[%s]"]', $option))
            ->getParent()
        ;

        if ($multiple && is_array($value)) {
            AutocompleteHelper::chooseValues($this->getSession(), $ruleAutocomplete, $value);

            return;
        }

        AutocompleteHelper::chooseValue($this->getSession(), $ruleAutocomplete, $value);
    }

    public function fillRuleOption($option, $value)
    {
        $this->getLastCollectionItem('rules')->fillField($option, $value);
    }

    public function makeExclusive()
    {
        $this->getDocument()->checkField('Exclusive');
    }

    public function checkChannel($name)
    {
        $this->getDocument()->checkField($name);
    }

    public function setStartsAt(\DateTimeInterface $dateTime)
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getDocument()->fillField('setono_sylius_bulk_discount_discount_startsAt_date', date('Y-m-d', $timestamp));
        $this->getDocument()->fillField('setono_sylius_bulk_discount_discount_startsAt_time', date('H:i', $timestamp));
    }

    public function setEndsAt(\DateTimeInterface $dateTime)
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getDocument()->fillField('setono_sylius_bulk_discount_discount_endsAt_date', date('Y-m-d', $timestamp));
        $this->getDocument()->fillField('setono_sylius_bulk_discount_discount_endsAt_time', date('H:i', $timestamp));
    }

    /**
     * @param string $channelName
     *
     * @return NodeElement
     */
    private function getChannelConfigurationOfLastRule($channelName)
    {
        return $this
            ->getLastCollectionItem('rules')
            ->find('css', sprintf('[id$="configuration"] .field:contains("%s")', $channelName))
        ;
    }

    /**
     * @param string $collection
     *
     * @return NodeElement
     */
    private function getLastCollectionItem($collection)
    {
        $items = $this->getCollectionItems($collection);

        Assert::notEmpty($items);

        return end($items);
    }

    /**
     * @param string $collection
     *
     * @return NodeElement[]
     */
    private function getCollectionItems($collection)
    {
        $items = $this->getElement($collection)->findAll('css', 'div[data-form-collection="item"]');

        Assert::isArray($items);

        return $items;
    }
}
