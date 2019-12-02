@managing_catalog_promotions
Feature: Deleting multiple catalog promotions
    In order to remove test, obsolete or incorrect catalog promotions in an efficient way
    As an Administrator
    I want to be able to delete multiple catalog promotions at once from the registry

    Background:
        Given the store operates on a single channel in "United States"
        And there is a catalog promotion "Christmas sale"
        And there is also a catalog promotion "New Year sale"
        And there is also a catalog promotion "Easter sale"
        And I am logged in as an administrator

# TODO Fix javascript builds in github actions before activating this feature
#    @ui @javascript
#    Scenario: Deleting multiple discounts at once
#        When I browse discounts
#        And I check the "Christmas sale" discount
#        And I check also the "New Year sale" discount
#        And I delete them
#        Then I should be notified that they have been successfully deleted
#        And I should see a single discount in the list
#        And I should see the discount "Easter sale" in the list
