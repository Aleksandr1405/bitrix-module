<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Entity\UserTable;

if (!\Bitrix\Main\Loader::includeModule('main') || !\Bitrix\Main\Loader::includeModule('iblock')) {
    ShowError(GetMessage("CUSTOM_USERS_LIST_MODULE_NOT_INSTALLED"));
    return;
}

$arResult = array();

$rsUsers = CUser::GetList(($by="ID"), ($order="desc"), array('GROUPS_ID' => array(1)));

while ($arUser = $rsUsers->Fetch()) {
    $arResult['USERS'][] = $arUser;
}

$this->IncludeComponentTemplate();


