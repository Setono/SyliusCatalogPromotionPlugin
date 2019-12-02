<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionPlugin\Behat\Page\Admin\Promotion;

use Behat\Mink\Element\NodeElement;
use Setono\SyliusCatalogPromotionPlugin\Model\PromotionInterface;
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
