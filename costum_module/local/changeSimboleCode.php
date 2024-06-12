<?php

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'update') {

    if (!\Bitrix\Main\Loader::includeModule('iblock') || !\Bitrix\Main\Loader::includeModule('catalog')) {
        echo json_encode(['log' => 'Ошибка подключения модулей.']);
        require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
        exit;
    }

    function transliterate($text)
    {
        $params = array(
            "max_len" => 255,
            "change_case" => 'L',
            "replace_space" => '_',
            "replace_other" => '_',
            "delete_repeat_replace" => true,
            "use_google" => false,
        );
        return CUtil::translit($text, "ru", $params);
    }

    function writeToCsv($filePath, $data)
    {
        $file = fopen($filePath, 'a');
        if ($file) {
            fputcsv($file, $data, ';');
            fclose($file);
        }
    }

    $iblockId = 22;
    $limit = 100;
    $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;

    $arFilter = array('IBLOCK_ID' => $iblockId);
    $arSelect = array('ID', 'IBLOCK_ID', 'CODE', 'NAME', 'PROPERTY_OLD_XML_ID', 'PROPERTY_OLD_ID', 'PROPERTY_OLD_DETAIL_PAGE_URL', 'DETAIL_PAGE_URL');
    $res = CIBlockElement::GetList(array('ID' => 'asc'), $arFilter, false, array("nPageSize" => $limit, "iNumPage" => ($start / $limit) + 1), $arSelect);

    $log = "";
    $processedElements = 0; // количество обработанных элементов в текущей итерации

    while ($element = $res->GetNext()) {

        $processedElements++;

        $oldXmlId = $element['PROPERTY_OLD_XML_ID_VALUE'];
        $oldId = $element['PROPERTY_OLD_ID_VALUE'];
        $oldDetailPageUrl = $element['PROPERTY_OLD_DETAIL_PAGE_URL_VALUE'];

        if (empty($oldXmlId)) {
            $oldXmlId = $element['CODE'];
        } elseif (empty($oldId)) {
            $oldId = $element['CODE'];
        }

        if (empty($oldDetailPageUrl)) {
            $oldDetailPageUrl = $element['DETAIL_PAGE_URL'];
        }

        $element['NAME'] = str_replace('+', ' plus ', $element['NAME']);
        $newCode = transliterate($element['NAME']);
        $newDetailPageUrl = str_replace($element['CODE'], $newCode, $element['DETAIL_PAGE_URL']);
        $newDetailPageUrl = str_replace('%2B', ' plus ', $newDetailPageUrl);

        $newUrlForOffers = '/products/' . $newCode . '/';

        /*$el = new CIBlockElement;

        if ($el->Update($element['ID'], array('CODE' => $newCode))) {
            CIBlockElement::SetPropertyValuesEx($element['ID'], $iblockId, array(
                'OLD_XML_ID' => $oldXmlId,
                'OLD_ID' => $oldId,
                'OLD_DETAIL_PAGE_URL' => $oldDetailPageUrl
            ));
            $log .= "Элемент " . $element['ID'] . " успешно обновлен.<br>";
        } else {
            $log .= "Ошибка обновления элемента " . $element['ID'] . ": " . $el->LAST_ERROR . "<br>";
        }*/
        $log .= "Элемент " . $element['ID'] . " успешно обновлен.<br>";

        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/upload/redirect_links.csv';
        $filePathToEmptyOffers = $_SERVER['DOCUMENT_ROOT'] . '/upload/noOfferProducts.csv';

        $firstOfferLogged = false;

        $offers = CCatalogSKU::getOffersList($element['ID'], $iblockId);
        if (!$offers) {
            writeToCsv($filePathToEmptyOffers, array($element['ID'], $oldDetailPageUrl));
        }

        if (!empty($offers[$element['ID']])) {
            foreach ($offers[$element['ID']] as $offerId => $offer) {
                $arOfferSelect = array('ID', 'IBLOCK_ID', 'NAME', 'CODE', 'DETAIL_PAGE_URL', 'PROPERTY_OLD_XML_ID', 'PROPERTY_OLD_ID', 'PROPERTY_OLD_DETAIL_PAGE_URL');
                $arOfferFilter = array('ID' => $offerId);
                $rsOffer = CIBlockElement::GetList(array(), $arOfferFilter, false, false, $arOfferSelect);
if ($arOffer = $rsOffer->GetNext()) {
                    $oldOfferXmlId = $arOffer['PROPERTY_OLD_XML_ID_VALUE'];
                    $oldOfferId = $arOffer['PROPERTY_OLD_ID_VALUE'];
                    $oldOfferDetailPageUrl = $arOffer['PROPERTY_OLD_DETAIL_PAGE_URL_VALUE'];

                    if (empty($oldOfferXmlId)) {
                        $oldOfferXmlId = $arOffer['CODE'];
                    } elseif (empty($oldOfferId)) {
                        $oldOfferId = $arOffer['CODE'];
                    }

                    if (empty($oldOfferDetailPageUrl)) {
                        $oldOfferDetailPageUrl = $arOffer['DETAIL_PAGE_URL'];
                    }

                    $arOffer['NAME'] = str_replace('+', ' plus ', $arOffer['NAME']);
                    $newOfferCode = transliterate($arOffer['NAME']);
                    $newOfferDetailPageUrl = $newUrlForOffers . $newOfferCode . '/';

                    /*if ($el->Update($offerId, array('CODE' => $newOfferCode))) {
                        CIBlockElement::SetPropertyValuesEx($offerId, $arOffer['IBLOCK_ID'], array(
                            'OLD_XML_ID' => $oldOfferXmlId,
                            'OLD_ID' => $oldOfferId,
                            'OLD_DETAIL_PAGE_URL' => $oldOfferDetailPageUrl
                        ));
                        $log .= "Торговое предложение $offerId успешно обновлено.<br>";
                    } else {
                        $log .= "Ошибка обновления торгового предложения $offerId: " . $el->LAST_ERROR . "<br>";
                    }*/
                    $log .= "Торговое предложение $offerId успешно обновлено.<br>";

                    if (!$firstOfferLogged) {
                        writeToCsv($filePath, array($element['ID'], $oldDetailPageUrl, $newOfferDetailPageUrl));
                        $firstOfferLogged = true;
                    }
                }
            }
        }
    }

    // Устанавливаем флаг $hasMore в false, если количество обработанных элементов меньше лимита
    if ($processedElements < $limit) {
        $hasMore = false;
        $log .= "ВСЕ ОБНОВЛЕННО.<br>";
    } else {
        $hasMore = true;
    }

    echo json_encode(['log' => $log, 'hasMore' => $hasMore, 'nextStart' => $start + $limit]);

    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Elements</title>
    <style>
        .container {
            margin: 0 auto;
            max-width: 1200px;
            padding: 30px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            background: #4c4cee;
            color: white;
            font-size: 19px;
            border: 1px solid white;
            margin-bottom: 30px;
        }

        #log {
            width: 100%;
            height: 300px;
            border: 1px solid #ccc;
            overflow-y: scroll;
            padding: 10px;
            background: #f9f9f9;
        }
    </style>
    <script>
        var start = 0;
        var processing = false;

        function updateElements() {
            if (processing) return;
            processing = true;
var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    document.getElementById('log').innerHTML += response.log;
                    document.getElementById('log').scrollTop = document.getElementById('log').scrollHeight;
                    if (response.hasMore) {
                        start = response.nextStart;
                        processing = false;
                        updateElements();
                    } else {
                        processing = false;
                    }
                } else if (xhr.readyState === 4) {
                    document.getElementById('log').innerHTML += 'Error: ' + xhr.status + '<br>';
                    processing = false;
                }
            };
            xhr.send('action=update&start=' + start);
        }
    </script>
</head>
<body>
<div class="container">
    <h1>Скрипт для обновления символьных кодов товаров и торговых предложений</h1>
    <button class="btn" onclick="updateElements()">Запустить обновление</button>
    <div id="log"></div>
</div>
</body>
</html>
