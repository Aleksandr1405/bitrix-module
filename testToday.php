<?php

use Bitrix\Crm\Service;
use Bitrix\Crm\Service\Container;
use Bitrix\Main\Loader;
Loader::includeModule('crm');

use Bitrix\Crm\Item;

$title = "22222";


function createSmartProcessElement($title)
{
// Создание элемента смарт-процесса
$typeid = 135; // Замените на соответствующий идентификатор смарт-процесса типа int
$factory = Container::getInstance()->getFactory($typeid);

if (!$factory) {
throw new \Exception('Фабрика не найдена для указанного типа');
}

$item = $factory->createItem([
Item::FIELD_NAME_STAGE_ID => 'DT' . $typeid . '_4:NEW',
]);

$data = [

'FIELDS[TITLE]' => $title,
 'UF_CRM_2_1686904208' => $title,
];

$item->setFromCompatibleData($fields);


try {
$result = $item->save();
$elementId = $result->getId();

// Возвращение созданного элемента
return $elementId;
} catch (\Exception $e) {
// Обработка ошибки при сохранении элемента
throw new \Exception('Ошибка при создании элемента: ' . $e->getMessage());
}
}

try {
$elementId = createSmartProcessElement($title);
echo json_encode(['success' => true, 'elementId' => $elementId]);
} catch (\Exception $e) {
echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}