@managing_catalog_promotions
Feature: Adding a new catalog promotion with action
    In order to give possibility to pay specifically less price for some goods
    As an Administrator
    I want to add a new catalog promotion with action to the registry

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new catalog promotion with fixed discount
        When I want to create a new catalog promotion
        And I specify its code as "10_off_for_all_mugs"
        And I name it "10% off for all mugs!"
        And I specify 10% action percent
        And I add it
        Then I should be notified that it has been successfully created
        And the "10% off for all mugs!" catalog promotion should appear in the registry

    @ui
    Scenario: Adding a new catalog promotion with fixed margin
        When I want to create a new catalog promotion
        And I specify its code as "20_margin_for_all_tshirts"
        And I name it "20% margin for all t-shirts!"
        And I specify 20% margin
        And I add it
        Then I should be notified that it has been successfully created
        And the "20% margin for all t-shirts!" catalog promotion should appear in the registry
