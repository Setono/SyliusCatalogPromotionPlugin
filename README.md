# SetonoSyliusBulkSpecialsPlugin

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

## Install

### Add plugin to composer.json

```bash
composer require setono/sylius-bulk-specials-plugin
```

### Register plugin at AppKernel.php

```php
# app/AppKernel.php

final class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        // Its important to instantiate SetonoSyliusBulkSpecialsPlugin
        // before calling parent::registerBundles()
        return array_merge([
            new \Setono\SyliusBulkSpecialsPlugin\SetonoSyliusBulkSpecialsPlugin(),
        ], parent::registerBundles(), [
            // ...
        ]);
    }
}
```

**Note**, that we MUST instantiate `SetonoSyliusBulkSpecialsPlugin` 
BEFORE `SyliusGridBundle` (which instantiates at `parent::registerBundles()`). 
Otherwise you'll see exception like this:

```bash
You have requested a non-existent parameter "setono_sylius_bulk_specials.model.special.class".  
```

### Add routing

```yaml
# app/config/routing.yml
setono_sylius_bulk_specials_admin:
    resource: "@SetonoSyliusBulkSpecialsPlugin/Resources/config/admin_routing.yml"
    prefix: /admin
```

### Extend `Product` model and `ProductRepository`

(see [test/Application](test/Application) for more details how to configure)

* Override config

    ```yaml
    # app/config/config.yml
    sylius_product:
        resources:
            product:
                classes:
                    model: AppBundle\Model\Product
                    repository: AppBundle\Doctrine\ORM\ProductRepository
    ```

* Override model

    ```php
    <?php
    
    namespace AppBundle\Model;
    
    use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
    use Setono\SyliusBulkSpecialsPlugin\Model\Traits\SpecialSubjectTrait;
    use Sylius\Component\Core\Model\Product as BaseProduct;
    
    /**
     * Class Product
     * @package AppBundle\Model
     */
    class Product extends BaseProduct implements SpecialSubjectInterface
    {
        use SpecialSubjectTrait {
            SpecialSubjectTrait::__construct as private __specialSubjectTraitConstruct;
        }
    
        public function __construct()
        {
            $this->__specialSubjectTraitConstruct();
    
            parent::__construct();
        }
    }
    ```
    
* Override mapping

    ```xml
    <?xml version="1.0" encoding="UTF-8"?>
    
    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:gedmo="http://gediminasm.org/schemas/orm/doctrine-extensions-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                          http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">
    
        <mapped-superclass name="AppBundle\Model\Product">
            <many-to-many field="specials" target-entity="Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface">
                <cascade>
                    <cascade-persist />
                </cascade>
                <order-by>
                    <order-by-field name="priority" direction="DESC" />
                </order-by>
                <join-table name="setono_sylius_builk_specials_products">
                    <join-columns>
                        <join-column name="special_id" referenced-column-name="id" nullable="false" on-delete="CASCADE" />
                    </join-columns>
                    <inverse-join-columns>
                        <join-column name="channel_id" referenced-column-name="id" nullable="false" on-delete="CASCADE" />
                    </inverse-join-columns>
                </join-table>
            </many-to-many>
        </mapped-superclass>
    
    </doctrine-mapping>
    
    ```

* Override repository

    ```php
    <?php
    # Doctrine/ORM/ProductRepository.php
    
    declare(strict_types=1);
    
    namespace AppBundle\Doctrine\ORM;
    
    use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\ProductRepositoryTrait;
    use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\ProductRepositoryInterface;
    use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;
    
    /**
     * Class ProductRepository
     */
    class ProductRepository extends BaseProductRepository implements ProductRepositoryInterface
    {
        use ProductRepositoryTrait;
    }
    ``` 

### Update your schema (for existing project)

```bash
bin/console doctrine:schema:update --force
```

# (Manually) Test plugin

- Run application:
  (by default application have default config at `dev` environment
  and example config from `Configure plugin` step at `prod` environment)
  
    ```bash
    SYMFONY_ENV=dev
    cd tests/Application && \
        yarn install && \
        yarn run gulp && \
        bin/console assets:install web -e $SYMFONY_ENV && \
        bin/console doctrine:database:create -e $SYMFONY_ENV && \
        bin/console doctrine:schema:create -e $SYMFONY_ENV && \
        bin/console sylius:fixtures:load -e $SYMFONY_ENV && \
        bin/console server:run -d web -e $SYMFONY_ENV
    ```

- Log in at `http://localhost:8000/admin`
  with Sylius demo credentials:
  
  ```
  Login: sylius@example.com
  Password: sylius 
  ```

- ...

- See how much that item was ordered (or even added to cart depending on config)

[ico-version]: https://img.shields.io/packagist/v/setono/sylius-bulk-specials-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Setono/SyliusBulkSpecialsPlugin/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/SyliusBulkSpecialsPlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/setono/sylius-bulk-specials-plugin
[link-travis]: https://travis-ci.org/Setono/SyliusBulkSpecialsPlugin
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/SyliusBulkSpecialsPlugin
