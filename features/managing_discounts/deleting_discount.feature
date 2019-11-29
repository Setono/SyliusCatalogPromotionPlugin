@setono_sylius_catalog_promotions_managing_discounts
Feature: Deleting a discount
    In order to remove test, obsolete or incorrect discounts
    As an Administrator
    I want to be able to delete a discount from the registry

    Background:
        Given the store operates on a single channel in "United States"
        And there is a discount "Christmas sale"
        And I am logged in as an administrator

    @ui
    Scenario: Deleted discount should disappear from the registry
        When I delete a "Christmas sale" discount
        Then I should be notified that it has been successfully deleted
        And this discount should no longer exist in the discount registry
