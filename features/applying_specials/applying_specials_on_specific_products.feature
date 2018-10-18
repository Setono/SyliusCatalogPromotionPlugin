@applying_specials
Feature: Receiving percentage discount on specific products
    As an Administrator
    I want assign discounts to specific products

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has a product "PHP Mug" priced at "$20.00"
        And there is a special "T-Shirts special"
        And it gives "20%" off on a "PHP T-Shirt" product

    @ui
    Scenario: Receiving percentage discount only on items from specific taxon
        When I reassign specials
        Then price of product "PHP T-Shirt" should become "$80.00"
        And price of product "PHP Mug" still should be "$20.00"

    @ui
    Scenario: Receiving different discounts on products from different taxons
        Given there is a special "Mugs special"
        And it gives "50%" off on a "PHP Mug" product
        When I reassign specials
        Then price of product "PHP T-Shirt" should become "$80.00"
        And price of product "PHP Mug" still should be "$10.00"
