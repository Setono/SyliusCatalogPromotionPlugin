<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkDiscountPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Setono\SyliusBulkDiscountPlugin\Model\Discount;
use Setono\SyliusBulkDiscountPlugin\Model\DiscountInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\Setono\SyliusBulkDiscountPlugin\Behat\Page\Admin\Discount\CreatePageInterface;
use Tests\Setono\SyliusBulkDiscountPlugin\Behat\Page\Admin\Discount\IndexPageInterface;
use Tests\Setono\SyliusBulkDiscountPlugin\Behat\Page\Admin\Discount\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingDiscountsContext implements Context
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
     * @When I want to create a new discount
     */
    public function iWantToCreateANewDiscount(): void
    {
        $this->createPage->open();
    }

    /**
     * @Given I want to browse discounts
     * @When I browse discounts
     */
    public function iWantToBrowseDiscounts(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I specify :actionPercent% action percent
     * @When I do not specify its action percent
     */
    public function iSpecifyItsActionPercent($actionPercent = null): void
    {
        $this->createPage->specifyActionPercent((float)$actionPercent);
    }

    /**
     * @When I specify :discount% discount
     * @When I do not specify discount
     */
    public function iSpecifyItsDiscount($discount = null): void
    {
        $this->createPage->specifyActionType(Discount::ACTION_TYPE_OFF);
        $this->createPage->specifyActionPercent(floatval($discount));
    }

    /**
     * @When I specify :margin% margin
     * @When I do not specify margin
     */
    public function iSpecifyItsMargin($margin = null): void
    {
        $this->createPage->specifyActionType(Discount::ACTION_TYPE_INCREASE);
        $this->createPage->specifyActionPercent(floatval($margin));
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null): void
    {
        $this->createPage->specifyCode($code ?? '');
    }

    /**
     * @When I name it :name
     * @When I do not name it
     * @When I remove its name
     */
    public function iNameIt($name = null): void
    {
        $this->createPage->nameIt($name ?? '');
    }

    /**
     * @When I remove its priority
     */
    public function iRemoveItsPriority(): void
    {
        $this->updatePage->setPriority(null);
    }

    /**
     * @Then I should see the discount :discountName in the list
     * @Then the :discountName discount should appear in the registry
     * @Then the :discountName discount should exist in the registry
     * @Then this discount should still be named :discountName
     * @Then discount :discountName should still exist in the registry
     */
    public function theDiscountShouldAppearInTheRegistry(string $discountName): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $discountName]));
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @When I add the "Product having one of taxons" rule configured with :firstTaxon
     * @When I add the "Product having one of taxons" rule configured with :firstTaxon or :secondTaxon
     */
    public function iAddTheHasTaxonRuleConfiguredWith(...$taxons): void
    {
        $this->createPage->addRule('Product having one of taxons');
        $this->createPage->selectAutocompleteRuleOption('Taxons', $taxons, true);
    }

    /**
     * @When I add the "Product is one of" rule configured with the :productName product
     * @When I add the "Product is one of" rule configured with the :firstProductName or :secondProductName product
     */
    public function iAddTheRuleConfiguredWithTheProducts(...$productNames): void
    {
        $this->createPage->addRule('Product is one of');
        $this->createPage->selectAutocompleteRuleOption('Products', $productNames, true);
    }

    /**
     * @When I add the "Product is" rule configured with the :productName product
     */
    public function iAddTheRuleConfiguredWithTheProduct($productName): void
    {
        $this->createPage->addRule('Product is');
        $this->createPage->selectAutocompleteRuleOption('Product', $productName);
    }

    /**
     * @When I check (also) the :discountName discount
     */
    public function iCheckTheDiscount(string $discountName): void
    {
        $this->indexPage->checkResourceOnPage(['name' => $discountName]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @Then I should see a single discount in the list
     * @Then there should be :amount discounts
     */
    public function thereShouldBeDiscount(int $amount = 1): void
    {
        Assert::same($amount, $this->indexPage->countItems());
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element): void
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter discount %s.', $element));
    }

    /**
     * @Then I should be notified that a :element value should be a numeric value
     */
    public function iShouldBeNotifiedThatAMinimalValueShouldBeNumeric($element): void
    {
        $this->assertFieldValidationMessage($element, 'This value is not valid.');
    }

    /**
     * @Then I should be notified that discount with this code already exists
     */
    public function iShouldBeNotifiedThatDiscountWithThisCodeAlreadyExists(): void
    {
        Assert::same($this->createPage->getValidationMessage('code'), 'The discount with given code already exists.');
    }

    /**
     * @Then discount with :element :name should not be added
     */
    public function discountWithElementValueShouldNotBeAdded($element, $name): void
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $name]));
    }

    /**
     * @Then there should still be only one discount with :element :value
     */
    public function thereShouldStillBeOnlyOneDiscountWith($element, $value): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @When I make it exclusive
     */
    public function iMakeItExclusive(): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->makeExclusive();
    }

    /**
     * @Then the :discount discount should be exclusive
     */
    public function theDiscountShouldBeExclusive(DiscountInterface $discount): void
    {
        $this->assertIfFieldIsTrue($discount, 'exclusive');
    }

    /**
     * @When I make it applicable for the :channelName channel
     */
    public function iMakeItApplicableForTheChannel($channelName): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->checkChannel($channelName);
    }

    /**
     * @Then the :discount discount should be applicable for the :channelName channel
     */
    public function theDiscountShouldBeApplicableForTheChannel(DiscountInterface $discount, $channelName): void
    {
        $this->iWantToModifyADiscount($discount);

        Assert::true($this->updatePage->checkChannelsState($channelName));
    }

    /**
     * @Given I want to modify a :discount discount
     * @Given /^I want to modify (this discount)$/
     * @Then I should be able to modify a :discount discount
     */
    public function iWantToModifyADiscount(DiscountInterface $discount): void
    {
        $this->updatePage->open(['id' => $discount->getId()]);
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled(): void
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When /^I delete a ("([^"]+)" discount)$/
     * @When /^I try to delete a ("([^"]+)" discount)$/
     */
    public function iDeleteDiscount(DiscountInterface $discount): void
    {
        $this->sharedStorage->set('discount', $discount);

        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $discount->getName()]);
    }

    /**
     * @Then /^(this discount) should no longer exist in the discount registry$/
     */
    public function discountShouldNotExistInTheRegistry(DiscountInterface $discount): void
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $discount->getCode()]));
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure(): void
    {
        $this->notificationChecker->checkNotification(
            'Cannot delete, the discount is in use.',
            NotificationType::failure()
        );
    }

    /**
     * @When I make it available from :startsDate to :endsDate
     */
    public function iMakeItAvailableFromTo(\DateTimeInterface $startsDate, \DateTimeInterface $endsDate): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->setStartsAt($startsDate);
        $currentPage->setEndsAt($endsDate);
    }

    /**
     * @Then the :discount discount should be available from :startsDate to :endsDate
     */
    public function theDiscountShouldBeAvailableFromTo(DiscountInterface $discount, \DateTimeInterface $startsDate, \DateTimeInterface $endsDate): void
    {
        $this->iWantToModifyADiscount($discount);

        Assert::true($this->updatePage->hasStartsAt($startsDate));

        Assert::true($this->updatePage->hasEndsAt($endsDate));
    }

    /**
     * @Then I should be notified that discount cannot end before it start
     */
    public function iShouldBeNotifiedThatDiscountCannotEndBeforeItsEvenStart(): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage('ends_at'), 'End date cannot be set prior start date.');
    }

    /**
     * @Then I should be notified that this value should not be blank
     */
    public function iShouldBeNotifiedThatThisValueShouldNotBeBlank(): void
    {
        Assert::same(
            $this->createPage->getValidationMessageForAction(),
            'This value should not be blank.'
        );
    }

    /**
     * @Then I should be notified that the maximum value of discount is 100%
     */
    public function iShouldBeNotifiedThatTheMaximumValueOfDiscountIs100(): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage('action_percent'), 'The maximum value of discount is 100%.');

    }

    /**
     * @Then I should be notified that discount value must be at least 1%
     */
    public function iShouldBeNotifiedThatDiscountValueMustBeAtLeast0(): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage('action_percent'), 'The value of discount must be at least 1%.');
    }

    /**
     * @Then I should see :count discounts on the list
     */
    public function iShouldSeeDiscountsOnTheList($count): void
    {
        $actualCount = $this->indexPage->countItems();

        Assert::same(
            (int) $count,
            $actualCount,
            'There should be %s discount, but there\'s %2$s.'
        );
    }

    /**
     * @Then the first discount on the list should have :field :value
     */
    public function theFirstDiscountOnTheListShouldHave($field, $value): void
    {
        $fields = $this->indexPage->getColumnFields($field);
        $actualValue = reset($fields);

        Assert::same(
            $actualValue,
            $value,
            sprintf('Expected first discount\'s %s to be "%s", but it is "%s".', $field, $value, $actualValue)
        );
    }

    /**
     * @Then the last discount on the list should have :field :value
     */
    public function theLastDiscountOnTheListShouldHave($field, $value): void
    {
        $fields = $this->indexPage->getColumnFields($field);
        $actualValue = end($fields);

        Assert::same(
            $actualValue,
            $value,
            sprintf('Expected last discount\'s %s to be "%s", but it is "%s".', $field, $value, $actualValue)
        );
    }

    /**
     * @Given the :discount discount should have priority :priority
     */
    public function theDiscountsShouldHavePriority(DiscountInterface $discount, $priority): void
    {
        $this->iWantToModifyADiscount($discount);

        Assert::same($this->updatePage->getPriority(), $priority);
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage($element, $expectedMessage): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage($element), $expectedMessage);
    }

    /**
     * @param DiscountInterface $discount
     * @param string $field
     */
    private function assertIfFieldIsTrue(DiscountInterface $discount, $field): void
    {
        $this->iWantToModifyADiscount($discount);

        Assert::true($this->updatePage->hasResourceValues([$field => 1]));
    }
}
