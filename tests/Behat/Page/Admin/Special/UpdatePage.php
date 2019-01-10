<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkSpecialsPlugin\Behat\Page\Admin\Special;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Tests\Setono\SyliusBulkSpecialsPlugin\Behat\Behaviour\SpecifiesItsActionPercent;
use Tests\Setono\SyliusBulkSpecialsPlugin\Behat\Behaviour\SpecifiesItsActionType;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use NamesIt;
    use SpecifiesItsActionType;
    use SpecifiesItsActionPercent;
    use ChecksCodeImmutability;
    use PageDefinedElements;

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        $this->getDocument()->fillField('Priority', $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->getElement('priority')->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function checkChannelsState($channelName)
    {
        $field = $this->getDocument()->findField($channelName);

        return (bool) $field->getValue();
    }

    public function makeExclusive()
    {
        $this->getDocument()->checkField('Exclusive');
    }

    public function checkChannel($name)
    {
        $this->getDocument()->checkField($name);
    }

    /**
     * {@inheritdoc}
     */
    public function setStartsAt(\DateTimeInterface $dateTime)
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getDocument()->fillField('setono_sylius_bulk_specials_special_startsAt_date', date('Y-m-d', $timestamp));
        $this->getDocument()->fillField('setono_sylius_bulk_specials_special_startsAt_time', date('H:i', $timestamp));
    }

    /**
     * {@inheritdoc}
     */
    public function setEndsAt(\DateTimeInterface $dateTime)
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getDocument()->fillField('setono_sylius_bulk_specials_special_endsAt_date', date('Y-m-d', $timestamp));
        $this->getDocument()->fillField('setono_sylius_bulk_specials_special_endsAt_time', date('H:i', $timestamp));
    }

    /**
     * {@inheritdoc}
     */
    public function hasStartsAt(\DateTimeInterface $dateTime): bool
    {
        $timestamp = $dateTime->getTimestamp();

        return $this->getElement('starts_at_date')->getValue() === date('Y-m-d', $timestamp)
            && $this->getElement('starts_at_time')->getValue() === date('H:i', $timestamp);
    }

    /**
     * {@inheritdoc}
     */
    public function hasEndsAt(\DateTimeInterface $dateTime): bool
    {
        $timestamp = $dateTime->getTimestamp();

        return $this->getElement('ends_at_date')->getValue() === date('Y-m-d', $timestamp)
            && $this->getElement('ends_at_time')->getValue() === date('H:i', $timestamp);
    }

    /**
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    protected function getCodeElement()
    {
        return $this->getElement('code');
    }
}
