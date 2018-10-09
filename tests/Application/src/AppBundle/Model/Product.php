<?php

declare(strict_types=1);

namespace AppBundle\Model;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectTrait;
use Sylius\Component\Core\Model\Product as BaseProduct;

/**
 * Class Product
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
