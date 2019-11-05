@setono_sylius_bulk_discount_managing_discounts
Feature: Special unique code validation
    In order to uniquely identify discounts
    As an Administrator
    I want to be prevented from adding two discounts with the same code

    Background:
        Given the store operates on a single channel in "United States"
        And there is a discount "Abstract discount" identified by "ABSTRACT_SPECIAL" code
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add discount with taken code
        Given I want to create a new discount
        When I specify its code as "ABSTRACT_SPECIAL"
        And I name it "Abstract discount"
        And I try to add it
        Then I should be notified that discount with this code already exists
        And there should still be only one discount with code "ABSTRACT_SPECIAL"
