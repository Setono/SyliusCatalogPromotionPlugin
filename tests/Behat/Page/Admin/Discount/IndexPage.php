<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionsPlugin\Behat\Page\Admin\Discount;

use Behat\Mink\Element\NodeElement;
use Setono\SyliusCatalogPromotionsPlugin\Model\PromotionInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * @param string $header
     *
     * @return NodeElement
     */
    private function getSpecialFieldsWithHeader(PromotionInterface $special, $header)
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');
        $fields = $tableAccessor->getFieldFromRow($table, $tableAccessor->getRowWithFields($table, ['code' => $special->getCode()]), $header);

        return $fields;
    }
}
