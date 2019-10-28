<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkDiscountPlugin\Behat\Page\Admin\Special;

use Behat\Mink\Element\NodeElement;
use Setono\SyliusBulkDiscountPlugin\Model\DiscountInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * @param DiscountInterface $special
     * @param string $header
     *
     * @return NodeElement
     */
    private function getSpecialFieldsWithHeader(DiscountInterface $special, $header)
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');
        $fields = $tableAccessor->getFieldFromRow($table, $tableAccessor->getRowWithFields($table, ['code' => $special->getCode()]), $header);

        return $fields;
    }
}
