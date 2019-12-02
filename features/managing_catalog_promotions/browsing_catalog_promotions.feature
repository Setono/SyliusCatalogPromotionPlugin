@managing_catalog_promotions
Feature: Browsing catalog promotions
    In order to see all catalog promotions
    As an Administrator
    I want to browse existing catalog promotions

    Background:
        Given the store operates on a single channel in "United States"
        And there is a catalog promotion "Basic promotion"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing catalog promotions
        Given I want to browse catalog promotions
        Then I should see a single catalog promotion in the list
        And the "Basic promotion" catalog promotion should exist in the registry
