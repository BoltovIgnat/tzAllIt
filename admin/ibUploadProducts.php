<?
define('ADMIN_MODULE_NAME', 'boltovignat.parseCsv');
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/csv_data.php";
use Bitrix\Main\Loader;

use Bitrix\Main\SystemException;
use Bitrix\Main\Application;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;

echo '@@@@@@@@@@@@@@@@@@@@';

$csvFile = new CCSVData('R', true);
try
{
    $csvFile->LoadFile($_SERVER["DOCUMENT_ROOT"].'/upload/cezares_shower_ogr.csv');
}
catch (SystemException $exception)
{
    echo $exception->getMessage();
}

$csvFile->SetDelimiter(';');

$ColorEntity = GetEntityDataClass(1);
$BrandEntity = GetEntityDataClass(2);
$SeriesEntity = GetEntityDataClass(3);
$RazmerEntity = GetEntityDataClass(4);

$i = 0;

while ($arRes = $csvFile->Fetch()) {

        $Prop = explode("#", $arRes[5]);
        $Desc = explode("<br><br>", $arRes[6]);

        $DimensionsProp = explode(",", $Prop[2])[1];
        $ThicknessProp = explode(":", $Desc[8])[1];
        $ThicknessProp = (int) str_replace("мм", "", $ThicknessProp);
        $ConstractDoorProp = explode(":", $Desc[5])[1];
        $AssuranceProp = $Desc[16] . " " . $Desc[17];
        $DopInfoProp = explode(":", $Desc[14])[1];

        $parent = '';
        $arFilter = array('IBLOCK_ID' => 4,'NAME' => $arRes[3]);
        $rsSect = CIBlockSection::GetList(array(),$arFilter);
        while ($arSect = $rsSect->GetNext())
        {
            $parent = $arSect['ID'];
        }
        if (empty($parent)){
            $bs = new CIBlockSection;
            $arFields = Array(
                "ACTIVE" => 'Y',
                "IBLOCK_ID" => 4,
                "NAME" => $arRes[3]
            );
            $parent = $bs->Add($arFields);
        }

        $SeriesName = $arRes[1];
        $SeriesProp = GetHLDataByName($SeriesEntity, $SeriesName);
        if (empty($SeriesProp)){
            $resSre = AddHLDataByName($SeriesEntity, $SeriesName);
            $SeriesProp = GetHLDataByName($SeriesEntity, $SeriesName);
        }
        $SeriesProp = $SeriesProp[0][ID];

        $BrandName = $arRes[3];
        $BrandProp = GetHLDataByName($BrandEntity , $BrandName);
        if (empty($SeriesProp)){
            $resSre = AddHLDataByName($BrandEntity , $BrandName);
            $BrandProp = GetHLDataByName($BrandEntity , $BrandName);
        }
        $BrandProp = $BrandProp[0][ID];

        $ColorProfileName = explode(":", $Desc[9])[1];
        $ColorProfileProp = GetHLDataByName($ColorEntity , $ColorProfileName);
        if (empty($SeriesProp)){
            $resSre = AddHLDataByName($ColorEntity , $ColorProfileName);
            $BrandProp = GetHLDataByName($ColorEntity , $ColorProfileName);
        }
        $ColorProfileProp = $ColorProfileProp[0][ID];
        $FormProp = explode(":", $Desc[3])[1];
        $MaterialProp[0] = explode(":", $Desc[10])[1];
        $MaterialProp[1] = explode(":", $Desc[11])[1];

        $arParamsStr = array("replace_space"=>"-","replace_other"=>"-");
        $arFields = array(
            "ACTIVE" => "Y",
            "IBLOCK_ID" => 4,
            "IBLOCK_SECTION_ID" => $parent,
            "NAME" => $arRes[0],
            "CODE" => Cutil::translit($arRes[0],"ru",$arParamsStr),
            "PREVIEW_TEXT" => $arRes[6],
            "DETAIL_TEXT" => $arRes[6],
            "PROPERTY_VALUES" => array(
                "BRAND" => $BrandProp,
                "SERIES" => $SeriesProp,
                "DIMENSIONS" => $DimensionsProp,
                "COLOR" => $ColorProp,
                "MATERIAL" => $MaterialProp,
                "ASSURANCE" => $AssuranceProp,
                "DOPINFO" => $DopInfoProp,
                "COLORGLASS" => $ColorGlassProp,
                "COLORPROFILE" => $ColorProfileProp,
                "CONSTRACTDOOR" => $ConstractDoorProp,
                "THICKNESS" => $ThicknessProp,
                "FORM" => $FormProp,
            )
        );

        echo 'Add info block <br>';
    $oElement = new CIBlockElement();
    $idElement = $oElement->Add($arFields);

    $RazmerProp = str_replace("мм:", "", $DimensionsProp);
    $RazmerProp = explode(":", $RazmerProp);
    $LengthProp = $RazmerProp[1];
    $HeightProp = $RazmerProp[0];
    $RazmerName = $RazmerProp[0]." ".$RazmerProp[1];

    $RazmerProp = GetHLDataBy($RazmerEntity, 'RAZMER', $RazmerName);
    if (empty($RazmerProp)){
        $resSre = AddHLDataBy($RazmerEntity,'RAZMER', $RazmerName);
        $SeriesProp = GetHLDataBy($RazmerEntity,'RAZMER', $RazmerName);
    }
    $RazmerProp = $RazmerProp[0][ID];

    $fields = array(
        'ID' => $idElement,
        'QUANTITY_TRACE' => \Bitrix\Catalog\ProductTable::STATUS_DEFAULT,
        'CAN_BUY_ZERO' => \Bitrix\Catalog\ProductTable::STATUS_DEFAULT,
        'PROPERTY_VALUES' => array(
            "CML2_LINK" => $idElement,
            "ARTICLE" => $arRes[0],
            "COLOR" => $ColorProp,
            "COLORGLASS" => $ColorGlassProp,
            "COLORPROFILE" => $ColorProfileProp,
            "RAZMER" => $RazmerProp,
            "THICKNESS" => $ThicknessProp,
            "LENGTH" => $LengthProp,
            "HEIGHT" => $HeightProp,
        )
    );
    if (!$useStoreControl)
    {
        // выключен складской учет
        $fields['QUANTITY'] = 1;
    }
    // создание товара
    echo 'Add product <br>';
    $result = CCatalogProduct::Add($fields);
    if ($result)
    {
        // добавление цены
        $priceId = CPrice::Add(array(
            'PRODUCT_ID' => $idElement,
            'CATALOG_GROUP_ID' => 1,
            'PRICE' => 1000,
            'CURRENCY' => 'RUB'
        ) );
    }
    $i++;
}
echo 'Загрузилось '.$i.' позиций';
echo '@@@@@@@@@@@@@@@@@@@@';

function GetEntityDataClass($HlBlockId) {
    if (empty($HlBlockId) || $HlBlockId < 1)
    {
        return false;
    }
    $hlblock = HLBT::getById($HlBlockId)->fetch();
    $entity = HLBT::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    return $entity_data_class;
}
function GetHLDataByName($Entity, $name) {

    $rsData = $Entity::getList(array(
        'select' => array('ID'),
        'filter' => array('UF_NAME' => $name)
    ));
    while($el = $rsData->fetch()){
        $arHL[] = $el;
    }
    return $arHL;
}
function AddHLDataByName($Entity, $name) {

    $result = $Entity::add(array(
        'UF_NAME'         => $name
    ));
    return $result;
}
function GetHLDataBy($Entity, $col,  $name) {

    $rsData = $Entity::getList(array(
        'select' => array('ID'),
        'filter' => array('UF_'.$col => $name)
    ));
    while($el = $rsData->fetch()){
        $arHL[] = $el;
    }
    return $arHL;
}
function AddHLDataBy($Entity, $col,  $name) {

    $result = $Entity::add(array(
        'UF_'.$col         => $name
    ));
    return $result;
}
?>


<?require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';