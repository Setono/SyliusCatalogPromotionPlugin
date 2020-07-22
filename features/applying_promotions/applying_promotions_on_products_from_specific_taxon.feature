@setono_sylius_catalog_promotion_applying_promotions
Feature: Receiving percentage discount on products from specific taxon
    As an Administrator
    I want assign discounts to products from specific taxons

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And it belongs to "T-Shirts"
        And the store has a product "PHP Mug" priced at "$20.00"
        And it belongs to "Mugs"
        And there is a catalog promotion "T-Shirts promo"
        And it gives "20%" off on every product classified as "T-Shirts"

    @ui
    Scenario: Receiving percentage discount only on items from specific taxon
        When I reassign catalog promotions
        Then price of product "PHP T-Shirt" should become "$80.00"
        And price of product "PHP Mug" still should be "$20.00"

    @ui
    Scenario: Receiving different discounts on products from different taxons
        Given there is a catalog promotion "Mugs promo"
        And it gives "50%" off on every product classified as "Mugs"
        When I reassign catalog promotions
        Then price of product "PHP T-Shirt" should become "$80.00"
        And price of product "PHP Mug" still should be "$10.00"
