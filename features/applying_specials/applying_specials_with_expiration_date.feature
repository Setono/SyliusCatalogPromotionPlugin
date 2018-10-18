@applying_specials
Feature: Applying special with an expiration date
    As a Visitor
    I want to have special's discounts applied to products only if special is valid

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a special "Christmas sale"
        And it gives "10%" off on that product

    @ui
    Scenario: Receiving a discount from a special which does not expire
        Given this special expires tomorrow
        When I reassign specials
        And its price should become "$90.00"

    @ui
    Scenario: Receiving no discount from a valid but expired special
        Given this special has already expired
        When I reassign specials
        Then its price still should be "$100.00"

    @ui
    Scenario: Receiving a discount from a special which has already started
        Given this special has started yesterday
        When I reassign specials
        And its price should become "$90.00"

    @ui
    Scenario: Receiving no discount from a special that has not been started yet
        Given this special starts tomorrow
        When I reassign specials
        Then its price still should be "$100.00"
