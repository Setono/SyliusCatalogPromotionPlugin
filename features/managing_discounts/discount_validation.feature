@setono_sylius_catalog_promotion_managing_discounts
Feature: discount validation
    In order to avoid making mistakes when managing a discount
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new discount without specifying its code
        Given I want to create a new discount
        When I name it "No-VAT discount"
        And I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And discount with name "No-VAT discount" should not be added

    @ui
    Scenario: Trying to add a new discount without specifying its name
        Given I want to create a new discount
        When I specify its code as "no_vat_discount"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And discount with code "no_vat_discount" should not be added

    @ui
    Scenario: Adding a discount with start date set up after end date
        Given I want to create a new discount
        When I specify its code as "FULL_METAL_SPECIAL"
        And I name it "Full metal discount"
        And I make it available from "24.12.2017" to "12.12.2017"
        And I try to add it
        Then I should be notified that discount cannot end before it start

    @ui
    Scenario: Trying to remove name from existing discount
        Given there is a discount "Christmas sale"
        And I want to modify this discount
        When I remove its name
        And I try to save my changes
        Then I should be notified that name is required
        And this discount should still be named "Christmas sale"

    @ui
    Scenario: Trying to add start later then end date for existing discount
        Given there is a discount "Christmas sale"
        And I want to modify this discount
        And I make it available from "24.12.2017" to "12.12.2017"
        And I try to save my changes
        Then I should be notified that discount cannot end before it start

    @ui
    Scenario: Trying to add a new discount with a wrong percentage discount
        Given I want to create a new discount
        When I specify its code as "christmas_sale"
        And I name it "Christmas sale"
        And I specify 120% discount
        And I try to add it
        Then I should be notified that the maximum value of discount is 100%
        And discount with name "Christmas sale" should not be added

    @ui
    Scenario: Trying to add a new discount with a negative percentage discount
        Given I want to create a new discount
        When I specify its code as "christmas_sale"
        And I name it "Christmas sale"
        And I specify -20% discount
        And I try to add it
        Then I should be notified that discount value must be at least 1%
        And discount with name "Christmas sale" should not be added
