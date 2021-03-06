@setono_sylius_catalog_promotion_applying_promotions
Feature: Applying multiple catalog promotions to one product
    As an Administrator
    I product's price decrease based on all catalog promotions matching it

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And it belongs to "T-Shirts"
        And the store has a product "PHP Mug" priced at "$20.00"
        And it belongs to "Mugs"
        And there is a catalog promotion "-50% for ALL"
        And it gives "50%" off on every product classified as "T-Shirts" or "Mugs"
        And there is a catalog promotion "T-Shirts promo"
        And it gives "20%" off on every product classified as "T-Shirts"

    @ui
    Scenario: Receiving discount
        When I reassign catalog promotions
        Then price of product "PHP T-Shirt" should become "$40.00"
        And price of product "PHP Mug" should become "$10.00"
