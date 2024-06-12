// функция для разработчика
function dump($var, $params = array()) {
    global $USER;
    $arParams = array(
        "DEV"   => array(976, 2065),      // ID разработчика
        "ADMIN" => false,  // показать администраторам
        "ALL"   => false,  // показать всем
        "DIE"   => false,  // завершить дальнейшее исполнение кода
        "DEBUG" => false,  // отобразить по параметру DEBUG
    );
    foreach ($params as $param) {
        switch ($param) {
            case "admin": $arParams["ADMIN"] = true; break;
            case "all":   $arParams["ALL"]   = true; break;
            case "die":   $arParams["DIE"]   = true; break;
            case "debug": $arParams["DEBUG"] = true; break;
        }
    }

    if (in_array($USER->GetID(), $arParams["DEV"])  ($USER->IsAdmin() && $arParams["ADMIN"])  $arParams["ALL"] || (isset($_REQUEST["DEBUG"]) && $arParams["DEBUG"])) {
        $bt =  debug_backtrace();
        $bt = $bt[0];
        $dRoot = $_SERVER["DOCUMENT_ROOT"];
        $dRoot = str_replace("/","\\",$dRoot);
        $bt["file"] = str_replace($dRoot,"",$bt["file"]);
        $dRoot = str_replace("\\","/",$dRoot);
        $bt["file"] = str_replace($dRoot,"",$bt["file"]);

        echo "<div style='font-size:9pt; color:#000; background:#fff; border:1px dashed #000;'>";
            echo "<div style='padding:3px 5px; background:#99CCFF; font-weight:bold;'>File: ".$bt["file"]." [".$bt["line"]."]</div>";
            echo "<pre style='padding:10px;'>";
                print_r($var);
            echo "</pre></div>";

        if ($arParams["DIE"]) die();
    }
}

/**
 * Возврат окончания слова при склонении
 *
 * Функция возвращает окончание слова, в зависимости от примененного к ней числа
 * Например: 5 товаров, 1 товар, 3 товара
 *
 * @param int $value - число, к которому необходимо применить склонение
 * @param array $status - массив возможных окончаний
 * @return mixed
 */
function BITGetDeclNum($value=1, $status= array('','а','ов'))
{
    $array =array(2,0,1,1,1,2);
    return $status[($value%100>4 && $value%100<20)? 2 : $array[($value%10<5)?$value%10:5]];
}

function addSpanTitle($text)
{
    if($text != "") {
        $arText = explode(" ", $text);

        if(count($arText) >= 2) {
            $end = floor(count($arText) / 2) - 1;
            $arText[0] = "<span>".$arText[0];
            $arText[$end] = $arText[$end]."</span>";

            return implode(" ", $arText);
        } else return $text;
    } else return $text;
}

function UTimeToDateRus($utime, $day = true, $month = true, $year = true)
{   $return = array();
    $m = (int)date('m', (int)$utime);
    $d = date('d', (int)$utime);
    $y = '';
    if ($year)
        $y = date('Y', (int)$utime);

    $ms = array(
        1 => 'января',
        2 => 'февраля',
        3 => 'марта',
        4 => 'апреля',
        5 => 'мая',
        6 => 'июня',
        7 => 'июля',
        8 => 'августа',
        9 => 'сентября',
        10=> 'октября',
        11=> 'ноября',
        12=> 'декабря'
    );

    if($day)    $return[] = $d;
    if($month)  $return[] = $ms[$m];
    if($year)   $return[] = $y;

    return implode(" ", $return);
}

// добавление стилей тегам
function add_html_style($html, $tags)
{
    foreach ($tags as $tag => $style) {
        preg_match_all('/<' . $tag . '([\s].*?)?>/i', $html, $matchs, PREG_SET_ORDER);
        foreach ($matchs as $match) {
            $attrs = array();
            if (!empty($match[1])) {
                preg_match_all('/[ ]?(.*?)=[\"|\'](.*?)[\"|\'][ ]?/', $match[1], $chanks);
                if (!empty($chanks[1]) && !empty($chanks[2])) {
                    $attrs = array_combine($chanks[1], $chanks[2]);
                }
            }

            if (empty($attrs['style'])) {
                $attrs['style'] = $style;
            } else {
                $attrs['style'] = rtrim($attrs['style'], '; ') . '; ' . $style;
            }

            $compile = array();
            foreach ($attrs as $name => $value) {
                $compile[] = $name . '="' . $value . '"';
            }
$html = str_replace($match[0], '<' . $tag . ' ' . implode(' ', $compile) . '>', $html);
        }
    }

    return $html;
}


