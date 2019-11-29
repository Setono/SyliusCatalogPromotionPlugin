@setono_sylius_catalog_promotions_managing_discounts
Feature: Deleting multiple discounts
    In order to remove test, obsolete or incorrect discounts in an efficient way
    As an Administrator
    I want to be able to delete multiple discounts at once from the registry

    Background:
        Given the store operates on a single channel in "United States"
        And there is a discount "Christmas sale"
        And there is also a discount "New Year sale"
        And there is also a discount "Easter sale"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple discounts at once
        When I browse discounts
        And I check the "Christmas sale" discount
        And I check also the "New Year sale" discount
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single discount in the list
        And I should see the discount "Easter sale" in the list
