@setono_sylius_catalog_promotion_applying_promotions
Feature: Applying only catalog promotions enabled for given channel
    As an Administrator
    I want to have only available catalog promotions applied to products prices

    Background:
        Given the store operates on a single channel in the "United States" named "Web"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a catalog promotion "Holiday promo"
        And it gives "10%" off on that product

    @ui
    Scenario: Receiving discount when catalog promotion enabled for current channel
        When I reassign catalog promotions
        And its price should become "$90.00"
        And its original price should become "$100.00"

    @ui
    Scenario: Not receiving discount when catalog promotion is disabled for current channel
        Given the catalog promotion was disabled for the channel "Web"
        When I reassign catalog promotions
        Then its price still should be "$100.00"
        And its original price should become "$100.00"
