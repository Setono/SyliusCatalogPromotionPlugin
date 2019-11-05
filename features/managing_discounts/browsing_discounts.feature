@setono_sylius_bulk_discount_managing_discounts
Feature: Browsing discounts
    In order to see all discount
    As an Administrator
    I want to browse existing discounts

    Background:
        Given the store operates on a single channel in "United States"
        And there is a discount "Basic discount"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing discounts
        Given I want to browse discounts
        Then I should see a single discount in the list
        And the "Basic discount" discount should exist in the registry
