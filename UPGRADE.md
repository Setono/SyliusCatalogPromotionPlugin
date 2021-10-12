# UPGRADE NOTES

## From 0.1 to 0.2

1. Update database schema

    ```bash
    bin/console doctrine:migrations:diff
    bin/console doctrine:migrations:migrate
    ```

    And make sure you have something like this in it:

    ```
    ALTER TABLE setono_sylius_catalog_promotion__promotion CHANGE discount discount NUMERIC(10, 5) DEFAULT '0' NOT NULL;
    ```

2. Change rules configuration at your `catalog_promotion` fixtures:

    - **Taxon**

      Replace:

         ```
         rules:
           - type: "has_taxon"
             configuration:
                 - "caps"
         ```

      to:

         ```
         rules:
           - type: "has_taxon"
             configuration:
                 taxons: # <---
                     - "caps"
         ```

    - **Product**

      Replace:

         ```
         rules:
           - type: "contains_product"
             configuration: "santa-cap"
         ```

      to:

         ```
         rules:
           - type: "contains_product"
             configuration:
                 product: "santa-cap" # <---
         ```

    - **Products**
    
      Replace:
    
         ```
         rules:
           - type: "contains_products"
             configuration:
                 - "santa-cap"
         ```
    
      to:
    
         ```
         rules:
           - type: "contains_products"
             configuration:
                 products: # <---
                     - "santa-cap"
         ```
