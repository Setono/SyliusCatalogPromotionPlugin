@processing_catalog_promotions @cli
Feature: Process catalog promotions via cli
    In order to calculate prices on all products
    As a Developer
    I want to process all catalog promotions

    Background:
        Given the store operates on a single channel in "United States"

    Scenario: Process catalog promotions
        Given the store classifies its products as "T-Shirts" and "Other"
        And there is a disabled catalog promotion for all products with a 50% discount
        And there is a catalog promotion for all products with a 20% discount
        And there is a catalog promotion for taxon "T-Shirts" with a 10% discount
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And it belongs to "T-Shirts"
        And the store has a product "Javascript T-Shirt" priced at "$10.00"
        And it belongs to "T-Shirts"
        And the store has a product "Desert Eagle" priced at "$2000.00"
        And it belongs to "Other"
        When I run the process command
        Then the command should finish successfully
        And the price of product "Desert Eagle" should be "$1600.00" and the original price should be "$2000.00"
        And the price of product "PHP T-Shirt" should be "$72.00" and the original price should be "$100.00"
        And the price of product "Javascript T-Shirt" should be "$7.20" and the original price should be "$10.00"
