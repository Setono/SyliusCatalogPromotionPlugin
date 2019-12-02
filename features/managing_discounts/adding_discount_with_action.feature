@setono_sylius_catalog_promotion_managing_discounts
Feature: Adding a new discount with action
    In order to give possibility to pay specifically less price for some goods
    As an Administrator
    I want to add a new discount with action to the registry

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new discount with fixed discount
        When I want to create a new discount
        And I specify its code as "10_off_for_all_mugs"
        And I name it "10% off for all mugs!"
        And I specify 10% discount
        And I add it
        Then I should be notified that it has been successfully created
        And the "10% off for all mugs!" discount should appear in the registry

    @ui
    Scenario: Adding a new discount with fixed discount
        When I want to create a new discount
        And I specify its code as "20_margin_for_all_tshirts"
        And I name it "20% margin for all t-shirts!"
        And I specify 20% margin
        And I add it
        Then I should be notified that it has been successfully created
        And the "20% margin for all t-shirts!" discount should appear in the registry
