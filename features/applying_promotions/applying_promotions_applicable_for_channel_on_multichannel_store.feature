@setono_sylius_catalog_promotion_applying_promotions
Feature: Applying promotions only to configured channel prices
    As an Administrator
    I want discount applied only to specified channel prices

    Background:
        Given the store also operates on a channel named "United States"
        And the store also operates on another channel named "Canada"
        And the store has a product "PHP T-Shirt" priced at "$100.00" in "United States" channel
        And this product is also priced at "$110" in "Canada" channel
        And there is a catalog promotion "Holiday promo" applicable for "United States" channel
        And it gives "10%" off on a "PHP T-Shirt" product

    @ui
    Scenario: Receiving discount only on "United States" channel price
        When I reassign catalog promotions
        Then price of product "PHP T-Shirt" on channel "United States" should become "$90.00"
        And price of product "PHP T-Shirt" on channel "Canada" still should be "$110.00"
