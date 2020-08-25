@managing_catalog_promotions
Feature: Catalog promotion validation
    In order to avoid making mistakes when managing a catalog promotion
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new catalog promotion without specifying its code
        Given I want to create a new catalog promotion
        When I name it "No-VAT discount"
        And I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And catalog promotion with name "No-VAT discount" should not be added

    @ui
    Scenario: Trying to add a new discount without specifying its name
        Given I want to create a new catalog promotion
        When I specify its code as "no_vat_discount"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And catalog promotion with code "no_vat_discount" should not be added

    @ui
    Scenario: Adding a discount with start date set up after end date
        Given I want to create a new catalog promotion
        When I specify its code as "FULL_METAL_PROMO"
        And I name it "Full metal discount"
        And I make it available from "24.12.2017" to "12.12.2017"
        And I try to add it
        Then I should be notified that catalog promotion cannot end before it start

    @ui
    Scenario: Trying to remove name from existing discount
        Given there is a catalog promotion "Christmas sale"
        And I want to modify this catalog promotion
        When I remove its name
        And I try to save my changes
        Then I should be notified that name is required
        And this catalog promotion should still be named "Christmas sale"

    @ui
    Scenario: Trying to add start later then end date for existing discount
        Given there is a catalog promotion "Christmas sale"
        And I want to modify this catalog promotion
        And I make it available from "24.12.2017" to "12.12.2017"
        And I try to save my changes
        Then I should be notified that catalog promotion cannot end before it start

    @ui
    Scenario: Trying to add a new discount with a wrong percentage discount
        Given I want to create a new catalog promotion
        When I specify its code as "christmas_sale"
        And I name it "Christmas sale"
        And I specify 120% action percent
        And I try to add it
        Then I should be notified that catalog promotion discount range is 0% to 100%
        And catalog promotion with name "Christmas sale" should not be added

    @ui
    Scenario: Trying to add a new discount with a negative percentage discount
        Given I want to create a new catalog promotion
        When I specify its code as "christmas_sale"
        And I name it "Christmas sale"
        And I specify -20% action percent
        And I try to add it
        Then I should be notified that catalog promotion discount range is 0% to 100%
        And catalog promotion with name "Christmas sale" should not be added
