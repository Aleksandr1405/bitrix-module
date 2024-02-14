<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Iblock\ElementTable;

class ProductsListComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        $this->arResult['PRODUCTS'] = $this->getProducts();
        $this->includeComponentTemplate();
    }

    private function getProducts()
    {
        $arResult = array();

        // Получаем ID раздела из параметров компонента
        $sectionId = $this->arParams['SECTION_ID'];

        if (Loader::includeModule('iblock')) {
            // Выбираем товары из указанного раздела
            $res = ElementTable::getList(array(
                'filter' => array(
                    'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                    'IBLOCK_SECTION_ID' => $sectionId,
                    'ACTIVE' => 'Y',
                ),
                'select' => array('ID', 'NAME'),
            ));

            while ($product = $res->fetch()) {
                $arResult[] = $product;
            }
        }

        return $arResult;
    }
}
