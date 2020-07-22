@setono_sylius_catalog_promotion_applying_promotions
Feature: Applying catalog promotion with an expiration date
    As a Visitor
    I want to have catalog promotion's discounts applied to products only if catalog promotion is valid

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a catalog promotion "Christmas sale"
        And it gives "10%" off on that product

    @ui
    Scenario: Receiving a discount from a catalog promotion which does not expire
        Given this catalog promotion expires tomorrow
        When I reassign catalog promotions
        And its price should become "$90.00"

    @ui
    Scenario: Receiving no discount from a valid but expired catalog promotion
        Given this catalog promotion has already expired
        When I reassign catalog promotions
        Then its price still should be "$100.00"

    @ui
    Scenario: Receiving a discount from a catalog promotion which has already started
        Given this catalog promotion has started yesterday
        When I reassign catalog promotions
        And its price should become "$90.00"

    @ui
    Scenario: Receiving no discount from a catalog promotion that has not been started yet
        Given this catalog promotion starts tomorrow
        When I reassign catalog promotions
        Then its price still should be "$100.00"
