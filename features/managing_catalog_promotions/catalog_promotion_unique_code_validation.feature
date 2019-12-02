@managing_catalog_promotions
Feature: Catalog promotion unique code validation
    In order to uniquely identify catalog promotions
    As an Administrator
    I want to be prevented from adding two catalog promotions with the same code

    Background:
        Given the store operates on a single channel in "United States"
        And there is a catalog promotion "Catalog promotion" identified by "CATALOG_PROMOTION" code
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add catalog promotion with taken code
        Given I want to create a new catalog promotion
        When I specify its code as "CATALOG_PROMOTION"
        And I name it "New catalog promotion"
        And I try to add it
        Then I should be notified that catalog promotion with this code already exists
        And there should still be only one catalog promotion with code "CATALOG_PROMOTION"
