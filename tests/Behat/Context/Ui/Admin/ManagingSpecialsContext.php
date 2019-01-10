<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkSpecialsPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Setono\SyliusBulkSpecialsPlugin\Model\Special;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\Setono\SyliusBulkSpecialsPlugin\Behat\Page\Admin\Special\CreatePageInterface;
use Tests\Setono\SyliusBulkSpecialsPlugin\Behat\Page\Admin\Special\IndexPageInterface;
use Tests\Setono\SyliusBulkSpecialsPlugin\Behat\Page\Admin\Special\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingSpecialsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When I want to create a new special
     */
    public function iWantToCreateANewSpecial()
    {
        $this->createPage->open();
    }

    /**
     * @Given I want to browse specials
     * @When I browse specials
     */
    public function iWantToBrowseSpecials()
    {
        $this->indexPage->open();
    }

    /**
     * @When I specify :actionPercent% action percent
     * @When I do not specify its action percent
     */
    public function iSpecifyItsActionPercent($actionPercent = null)
    {
        $this->createPage->specifyActionPercent(floatval($actionPercent));
    }

    /**
     * @When I specify :discount% discount
     * @When I do not specify discount
     */
    public function iSpecifyItsDiscount($discount = null)
    {
        $this->createPage->specifyActionType(Special::ACTION_TYPE_OFF);
        $this->createPage->specifyActionPercent(floatval($discount));
    }

    /**
     * @When I specify :margin% margin
     * @When I do not specify margin
     */
    public function iSpecifyItsMargin($margin = null)
    {
        $this->createPage->specifyActionType(Special::ACTION_TYPE_INCREASE);
        $this->createPage->specifyActionPercent(floatval($margin));
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null)
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I name it :name
     * @When I do not name it
     * @When I remove its name
     */
    public function iNameIt($name = null)
    {
        $this->createPage->nameIt($name);
    }

    /**
     * @When I remove its priority
     */
    public function iRemoveItsPriority()
    {
        $this->updatePage->setPriority(null);
    }

    /**
     * @Then I should see the special :specialName in the list
     * @Then the :specialName special should appear in the registry
     * @Then the :specialName special should exist in the registry
     * @Then this special should still be named :specialName
     * @Then special :specialName should still exist in the registry
     */
    public function theSpecialShouldAppearInTheRegistry(string $specialName): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $specialName]));
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @When I add the "Product having one of taxons" rule configured with :firstTaxon
     * @When I add the "Product having one of taxons" rule configured with :firstTaxon or :secondTaxon
     */
    public function iAddTheHasTaxonRuleConfiguredWith(...$taxons)
    {
        $this->createPage->addRule('Product having one of taxons');
        $this->createPage->selectAutocompleteRuleOption('Taxons', $taxons, true);
    }

    /**
     * @When I add the "Product is one of" rule configured with the :productName product
     * @When I add the "Product is one of" rule configured with the :firstProductName or :secondProductName product
     */
    public function iAddTheRuleConfiguredWithTheProducts(...$productNames)
    {
        $this->createPage->addRule('Product is one of');
        $this->createPage->selectAutocompleteRuleOption('Products', $productNames, true);
    }

    /**
     * @When I add the "Product is" rule configured with the :productName product
     */
    public function iAddTheRuleConfiguredWithTheProduct($productName)
    {
        $this->createPage->addRule('Product is');
        $this->createPage->selectAutocompleteRuleOption('Product', $productName);
    }

    /**
     * @When I check (also) the :specialName special
     */
    public function iCheckTheSpecial(string $specialName): void
    {
        $this->indexPage->checkResourceOnPage(['name' => $specialName]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @Then I should see a single special in the list
     * @Then there should be :amount specials
     */
    public function thereShouldBeSpecial(int $amount = 1): void
    {
        Assert::same($amount, $this->indexPage->countItems());
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter special %s.', $element));
    }

    /**
     * @Then I should be notified that a :element value should be a numeric value
     */
    public function iShouldBeNotifiedThatAMinimalValueShouldBeNumeric($element)
    {
        $this->assertFieldValidationMessage($element, 'This value is not valid.');
    }

    /**
     * @Then I should be notified that special with this code already exists
     */
    public function iShouldBeNotifiedThatSpecialWithThisCodeAlreadyExists()
    {
        Assert::same($this->createPage->getValidationMessage('code'), 'The special with given code already exists.');
    }

    /**
     * @Then special with :element :name should not be added
     */
    public function specialWithElementValueShouldNotBeAdded($element, $name)
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $name]));
    }

    /**
     * @Then there should still be only one special with :element :value
     */
    public function thereShouldStillBeOnlyOneSpecialWith($element, $value)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @When I make it exclusive
     */
    public function iMakeItExclusive()
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->makeExclusive();
    }

    /**
     * @Then the :special special should be exclusive
     */
    public function theSpecialShouldBeExclusive(SpecialInterface $special)
    {
        $this->assertIfFieldIsTrue($special, 'exclusive');
    }

    /**
     * @When I make it applicable for the :channelName channel
     */
    public function iMakeItApplicableForTheChannel($channelName)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->checkChannel($channelName);
    }

    /**
     * @Then the :special special should be applicable for the :channelName channel
     */
    public function theSpecialShouldBeApplicableForTheChannel(SpecialInterface $special, $channelName)
    {
        $this->iWantToModifyASpecial($special);

        Assert::true($this->updatePage->checkChannelsState($channelName));
    }

    /**
     * @Given I want to modify a :special special
     * @Given /^I want to modify (this special)$/
     * @Then I should be able to modify a :special special
     */
    public function iWantToModifyASpecial(SpecialInterface $special)
    {
        $this->updatePage->open(['id' => $special->getId()]);
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When /^I delete a ("([^"]+)" special)$/
     * @When /^I try to delete a ("([^"]+)" special)$/
     */
    public function iDeleteSpecial(SpecialInterface $special)
    {
        $this->sharedStorage->set('special', $special);

        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $special->getName()]);
    }

    /**
     * @Then /^(this special) should no longer exist in the special registry$/
     */
    public function specialShouldNotExistInTheRegistry(SpecialInterface $special)
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $special->getCode()]));
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        $this->notificationChecker->checkNotification(
            'Cannot delete, the special is in use.',
            NotificationType::failure()
        );
    }

    /**
     * @When I make it available from :startsDate to :endsDate
     */
    public function iMakeItAvailableFromTo(\DateTimeInterface $startsDate, \DateTimeInterface $endsDate)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->setStartsAt($startsDate);
        $currentPage->setEndsAt($endsDate);
    }

    /**
     * @Then the :special special should be available from :startsDate to :endsDate
     */
    public function theSpecialShouldBeAvailableFromTo(SpecialInterface $special, \DateTimeInterface $startsDate, \DateTimeInterface $endsDate)
    {
        $this->iWantToModifyASpecial($special);

        Assert::true($this->updatePage->hasStartsAt($startsDate));

        Assert::true($this->updatePage->hasEndsAt($endsDate));
    }

    /**
     * @Then I should be notified that special cannot end before it start
     */
    public function iShouldBeNotifiedThatSpecialCannotEndBeforeItsEvenStart()
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage('ends_at'), 'End date cannot be set prior start date.');
    }

    /**
     * @Then I should be notified that this value should not be blank
     */
    public function iShouldBeNotifiedThatThisValueShouldNotBeBlank()
    {
        Assert::same(
            $this->createPage->getValidationMessageForAction(),
            'This value should not be blank.'
        );
    }

    /**
     * @Then I should be notified that the maximum value of discount is 100%
     */
    public function iShouldBeNotifiedThatTheMaximumValueOfDiscountIs100()
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage('action_percent'), 'The maximum value of discount is 100%.');

    }

    /**
     * @Then I should be notified that discount value must be at least 0%
     */
    public function iShouldBeNotifiedThatDiscountValueMustBeAtLeast0()
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage('action_percent'), 'The value of discount must be at least 0%.');
    }

    /**
     * @Then I should see :count specials on the list
     */
    public function iShouldSeeSpecialsOnTheList($count)
    {
        $actualCount = $this->indexPage->countItems();

        Assert::same(
            (int) $count,
            $actualCount,
            'There should be %s special, but there\'s %2$s.'
        );
    }

    /**
     * @Then the first special on the list should have :field :value
     */
    public function theFirstSpecialOnTheListShouldHave($field, $value)
    {
        $fields = $this->indexPage->getColumnFields($field);
        $actualValue = reset($fields);

        Assert::same(
            $actualValue,
            $value,
            sprintf('Expected first special\'s %s to be "%s", but it is "%s".', $field, $value, $actualValue)
        );
    }

    /**
     * @Then the last special on the list should have :field :value
     */
    public function theLastSpecialOnTheListShouldHave($field, $value)
    {
        $fields = $this->indexPage->getColumnFields($field);
        $actualValue = end($fields);

        Assert::same(
            $actualValue,
            $value,
            sprintf('Expected last special\'s %s to be "%s", but it is "%s".', $field, $value, $actualValue)
        );
    }

    /**
     * @Given the :special special should have priority :priority
     */
    public function theSpecialsShouldHavePriority(SpecialInterface $special, $priority)
    {
        $this->iWantToModifyASpecial($special);

        Assert::same($this->updatePage->getPriority(), $priority);
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage($element, $expectedMessage)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage($element), $expectedMessage);
    }

    /**
     * @param SpecialInterface $special
     * @param string $field
     */
    private function assertIfFieldIsTrue(SpecialInterface $special, $field)
    {
        $this->iWantToModifyASpecial($special);

        Assert::true($this->updatePage->hasResourceValues([$field => 1]));
    }
}
