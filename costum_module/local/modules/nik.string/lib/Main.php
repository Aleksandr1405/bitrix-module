<?php
namespace Nik\String;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;

class Main{

    public function appendScriptsToPage(){

        if(!defined("ADMIN_SECTION") && $ADMIN_SECTION !== true){

            $module_id = pathinfo(dirname(__DIR__))["basename"];

            Asset::getInstance()->addString(
                "<script id=\"".str_replace(".", "_", $module_id)."-params\" data-params='".json_encode(
                    array(
                        "switch_on"   => Option::get($module_id, "switch_on", "Y"),
                        "text"       => Option::get($module_id, "text", "qwerty123"),
                    )
                )."'></script>",
                true
            );


            Asset::getInstance()->addJs("/bitrix/js/".$module_id."/jquery.min.js");
            Asset::getInstance()->addJs("/bitrix/js/".$module_id."/script.min.js");

            Asset::getInstance()->addCss("/bitrix/css/".$module_id."/style.min.css");
        }

        return false;
    }
}