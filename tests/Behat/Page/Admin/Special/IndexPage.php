<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkSpecialsPlugin\Behat\Page\Admin\Special;

use Behat\Mink\Element\NodeElement;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * @param SpecialInterface $special
     * @param string $header
     *
     * @return NodeElement
     */
    private function getSpecialFieldsWithHeader(SpecialInterface $special, $header)
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');
        $fields = $tableAccessor->getFieldFromRow($table, $tableAccessor->getRowWithFields($table, ['code' => $special->getCode()]), $header);

        return $fields;
    }
}
