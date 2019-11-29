<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionsPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Setono\SyliusCatalogPromotionsPlugin\Model\Promotion;
use Setono\SyliusCatalogPromotionsPlugin\Model\PromotionInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\Setono\SyliusCatalogPromotionsPlugin\Behat\Page\Admin\Discount\CreatePageInterface;
use Tests\Setono\SyliusCatalogPromotionsPlugin\Behat\Page\Admin\Discount\IndexPageInterface;
use Tests\Setono\SyliusCatalogPromotionsPlugin\Behat\Page\Admin\Discount\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingDiscountsContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var IndexPageInterface */
    private $indexPage;

    /** @var CreatePageInterface */
    private $createPage;

    /** @var UpdatePageInterface */
    private $updatePage;

    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    /** @var NotificationCheckerInterface */
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
     * @When I want to create a new promotion
     */
    public function iWantToCreateANewDiscount(): void
    {
        $this->createPage->open();
    }

    /**
     * @Given I want to browse promotions
     * @When I browse promotions
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
        $this->createPage->specifyActionPercent((float) $actionPercent);
    }

    /**
     * @When I specify :promotion% promotion
     * @When I do not specify promotion
     */
    public function iSpecifyItsDiscount($promotion = null): void
    {
        $this->createPage->specifyActionType(Promotion::ACTION_TYPE_OFF);
        $this->createPage->specifyActionPercent((float) $promotion);
    }

    /**
     * @When I specify :margin% margin
     * @When I do not specify margin
     */
    public function iSpecifyItsMargin($margin = null): void
    {
        $this->createPage->specifyActionType(Promotion::ACTION_TYPE_INCREASE);
        $this->createPage->specifyActionPercent((float) $margin);
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
     * @Then I should see the promotion :promotionName in the list
     * @Then the :promotionName promotion should appear in the registry
     * @Then the :promotionName promotion should exist in the registry
     * @Then this promotion should still be named :promotionName
     * @Then promotion :promotionName should still exist in the registry
     */
    public function theDiscountShouldAppearInTheRegistry(string $promotionName): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $promotionName]));
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
     * @When I check (also) the :promotionName promotion
     */
    public function iCheckTheDiscount(string $promotionName): void
    {
        $this->indexPage->checkResourceOnPage(['name' => $promotionName]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @Then I should see a single promotion in the list
     * @Then there should be :amount promotions
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
        $this->assertFieldValidationMessage($element, sprintf('Please enter promotion %s.', $element));
    }

    /**
     * @Then I should be notified that a :element value should be a numeric value
     */
    public function iShouldBeNotifiedThatAMinimalValueShouldBeNumeric($element): void
    {
        $this->assertFieldValidationMessage($element, 'This value is not valid.');
    }

    /**
     * @Then I should be notified that promotion with this code already exists
     */
    public function iShouldBeNotifiedThatDiscountWithThisCodeAlreadyExists(): void
    {
        Assert::same($this->createPage->getValidationMessage('code'), 'The promotion with given code already exists.');
    }

    /**
     * @Then promotion with :element :name should not be added
     */
    public function promotionWithElementValueShouldNotBeAdded($element, $name): void
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $name]));
    }

    /**
     * @Then there should still be only one promotion with :element :value
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
     * @Then the :promotion promotion should be exclusive
     */
    public function theDiscountShouldBeExclusive(PromotionInterface $promotion): void
    {
        $this->assertIfFieldIsTrue($promotion, 'exclusive');
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
     * @Then the :promotion promotion should be applicable for the :channelName channel
     */
    public function theDiscountShouldBeApplicableForTheChannel(PromotionInterface $promotion, $channelName): void
    {
        $this->iWantToModifyADiscount($promotion);

        Assert::true($this->updatePage->checkChannelsState($channelName));
    }

    /**
     * @Given I want to modify a :promotion promotion
     * @Given /^I want to modify (this promotion)$/
     * @Then I should be able to modify a :promotion promotion
     */
    public function iWantToModifyADiscount(PromotionInterface $promotion): void
    {
        $this->updatePage->open(['id' => $promotion->getId()]);
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
     * @When /^I delete a ("([^"]+)" promotion)$/
     * @When /^I try to delete a ("([^"]+)" promotion)$/
     */
    public function iDeleteDiscount(PromotionInterface $promotion): void
    {
        $this->sharedStorage->set('promotion', $promotion);

        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $promotion->getName()]);
    }

    /**
     * @Then /^(this promotion) should no longer exist in the promotion registry$/
     */
    public function promotionShouldNotExistInTheRegistry(PromotionInterface $promotion): void
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $promotion->getCode()]));
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure(): void
    {
        $this->notificationChecker->checkNotification(
            'Cannot delete, the promotion is in use.',
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
     * @Then the :promotion promotion should be available from :startsDate to :endsDate
     */
    public function theDiscountShouldBeAvailableFromTo(PromotionInterface $promotion, \DateTimeInterface $startsDate, \DateTimeInterface $endsDate): void
    {
        $this->iWantToModifyADiscount($promotion);

        Assert::true($this->updatePage->hasStartsAt($startsDate));

        Assert::true($this->updatePage->hasEndsAt($endsDate));
    }

    /**
     * @Then I should be notified that promotion cannot end before it start
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
     * @Then I should be notified that the maximum value of promotion is 100%
     */
    public function iShouldBeNotifiedThatTheMaximumValueOfDiscountIs100(): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage('action_percent'), 'The maximum value of promotion is 100%.');
    }

    /**
     * @Then I should be notified that promotion value must be at least 1%
     */
    public function iShouldBeNotifiedThatDiscountValueMustBeAtLeast0(): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage('action_percent'), 'The value of promotion must be at least 1%.');
    }

    /**
     * @Then I should see :count promotions on the list
     */
    public function iShouldSeeDiscountsOnTheList($count): void
    {
        $actualCount = $this->indexPage->countItems();

        Assert::same(
            (int) $count,
            $actualCount,
            'There should be %s promotion, but there\'s %2$s.'
        );
    }

    /**
     * @Then the first promotion on the list should have :field :value
     */
    public function theFirstDiscountOnTheListShouldHave($field, $value): void
    {
        $fields = $this->indexPage->getColumnFields($field);
        $actualValue = reset($fields);

        Assert::same(
            $actualValue,
            $value,
            sprintf('Expected first promotion\'s %s to be "%s", but it is "%s".', $field, $value, $actualValue)
        );
    }

    /**
     * @Then the last promotion on the list should have :field :value
     */
    public function theLastDiscountOnTheListShouldHave($field, $value): void
    {
        $fields = $this->indexPage->getColumnFields($field);
        $actualValue = end($fields);

        Assert::same(
            $actualValue,
            $value,
            sprintf('Expected last promotion\'s %s to be "%s", but it is "%s".', $field, $value, $actualValue)
        );
    }

    /**
     * @Given the :promotion promotion should have priority :priority
     */
    public function theDiscountsShouldHavePriority(PromotionInterface $promotion, $priority): void
    {
        $this->iWantToModifyADiscount($promotion);

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
     * @param string $field
     */
    private function assertIfFieldIsTrue(PromotionInterface $promotion, $field): void
    {
        $this->iWantToModifyADiscount($promotion);

        Assert::true($this->updatePage->hasResourceValues([$field => 1]));
    }
}
