@managing_catalog_promotions
Feature: Deleting a catalog promotion
    In order to remove test, obsolete or incorrect catalog promotions
    As an Administrator
    I want to be able to delete a catalog promotion from the registry

    Background:
        Given the store operates on a single channel in "United States"
        And there is a catalog promotion "Christmas sale"
        And I am logged in as an administrator

    @ui
    Scenario: Deleted catalog promotion should disappear from the registry
        When I delete a "Christmas sale" catalog promotion
        Then I should be notified that it has been successfully deleted
        And this catalog promotion should no longer exist in the catalog promotion registry
