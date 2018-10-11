# SetonoSyliusBulkSpecialsPlugin

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

Plugin for Sylius 1.3 to define permanent or time-limited
Specials (discounts) for Products and automatically update prices.

Discounts calculated from `ChannelPrice`'s `originalPrice` field if it non-zero
and applies to `price` field. 

All calculations can be done immediately
(if you have not much products in your store) or asynchronously via queues.

## Install

### Add plugin to composer.json

```bash
composer require setono/sylius-bulk-specials-plugin
```

#### (optional) Add transport for enqueue bundle

(see https://github.com/php-enqueue/enqueue-dev/blob/master/docs/bundle/quick_tour.md
for more details)

```bash
composer require enqueue/fs
```

### Register plugin and enqueue bundle at AppKernel.php

```php
<?php
# config/bundles.php

return [
    // ...
    // Its important to instantiate SetonoSyliusBulkSpecialsPlugin
    // before calling parent::registerBundles()
    Setono\SyliusBulkSpecialsPlugin\SetonoSyliusBulkSpecialsPlugin::class => ['all' => true],
    Sylius\Bundle\GridBundle\SyliusGridBundle::class => ['all' => true],
    // ...
    // Uncomment if you want to use queues
    // Enqueue\Bundle\EnqueueBundle::class => ['all' => true],
    AppBundle\AppBundle::class => ['all' => true],
    // ...
];

```

**Note**, that we MUST instantiate `SetonoSyliusBulkSpecialsPlugin` 
BEFORE `SyliusGridBundle` (which instantiates at `parent::registerBundles()`). 
Otherwise you'll see exception like this:

```bash
You have requested a non-existent parameter "setono_sylius_bulk_specials.model.special.class".  
```

### Add config

```yaml
# config/packages/_sylius.yaml
imports:
    - { resource: "@SetonoSyliusBulkSpecialsPlugin/Resources/config/app/config.yml" }
```

#### (optional) Add proper enqueue bundle configuration

```yaml
# config/packages/_sylius.yaml

setono_sylius_bulk_specials:
    # If you want to use enqueue bundle to asynchronously handle
    # bulk actions - you should set `queue` parameter to true
    # and install/configure enqueue/enqueue-bundle with transport implementation
    queue: true

    # If your store have not more than 1000 products - you can use
    # plugin without any additional configuration or set `queue`
    # to default value (false)
    # queue: false

enqueue:
    transport:
        # Here we use enqueue/fs for testing as most simple transport implementation
        # @see https://enqueue.readthedocs.io/en/latest/transport/filesystem/
        default: fs
        fs:
            dsn: "file://%kernel.project_dir%/var/queue"
    client:
        traceable_producer: true
```

### Add routing

```yaml
# config/routes.yaml
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
                    controller: Setono\SyliusBulkSpecialsPlugin\Controller\ProductController
    ```

* Override model

    ```php
    <?php
    
    declare(strict_types=1);
    
    namespace AppBundle\Model;
    
    use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
    use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectTrait;
    use Sylius\Component\Core\Model\Product as BaseProduct;
    
    /**
     * Class Product
     */
    class Product extends BaseProduct implements ProductInterface
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
    use Setono\SyliusBulkSpecialsPlugin\Special\QueryBuilder\Rule\RuleQueryBuilderAwareInterface;
    use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;
    
    /**
     * Class ProductRepository
     */
    class ProductRepository extends BaseProductRepository
        implements ProductRepositoryInterface, RuleQueryBuilderAwareInterface
    {
        use ProductRepositoryTrait;
    }
    ``` 

* Override `ProductRepository` service definition as it shown at 
  [tests/Application/src/AppBundle/Resources/config/services.xml](tests/Application/src/AppBundle/Resources/config/services.xml).

### Update your schema (for existing project)

```bash
# Generate and edit migration
bin/console doctrine:migrations:diff

# Then apply migration
bin/console doctrine:migrations:migrate
```

### Install assets

```bash
bin/console sylius:install:assets
```

### Configure CRON to run next command every minute

```bash
bin/console setono:sylius-bulk-specials:check-active
```

This required to enable/disable Specials that have `startsAt`/`endsAt` defined.

### (optional) Configure `supervisord` on production if you use queues 

Make sure next command always in run state:

```bash
bin/console enqueue:consume 
```

This can be done with `supervisord`
(see [docs](https://enqueue.readthedocs.io/en/latest/bundle/production_settings/) for details):

```
[program:enqueue_message_consumer]
command=/path/to/app/console --env=prod --no-debug --time-limit="now + 5 minutes" enqueue:consume
process_name=%(program_name)s_%(process_num)02d
numprocs=4
autostart=true
autorestart=true
startsecs=0
user=apache
redirect_stderr=true
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
        bin/console assets:install public -e $SYMFONY_ENV && \
        bin/console doctrine:database:drop --force -e $SYMFONY_ENV && \
        bin/console doctrine:database:create -e $SYMFONY_ENV && \
        bin/console doctrine:schema:create -e $SYMFONY_ENV && \
        bin/console sylius:fixtures:load setono -e $SYMFONY_ENV && \
        bin/console server:run -d public -e $SYMFONY_ENV
    ```

- Log in at `http://localhost:8000/admin`
  with Sylius demo credentials:
  
  ```
  Login: sylius@example.com
  Password: sylius 
  ```

- ...

# TODO

- Tests
- Cleanup:
  - Improve translations
  - Remove (or no?) product_code, leave only product_codes rule
  - [Pull request to Sylius?] Bulk action buttons looks ugly in current design (solution is "float:left" in form's style)

[ico-version]: https://img.shields.io/packagist/v/setono/sylius-bulk-specials-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Setono/SyliusBulkSpecialsPlugin/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/SyliusBulkSpecialsPlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/setono/sylius-bulk-specials-plugin
[link-travis]: https://travis-ci.org/Setono/SyliusBulkSpecialsPlugin
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/SyliusBulkSpecialsPlugin
