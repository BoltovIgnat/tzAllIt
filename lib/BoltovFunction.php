<?php
namespace BoltovIgnat\ParseCsv;

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Entity\Validator;
use Bitrix\Main\Localization\Loc;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;

class BoltovFunction extends \CModule
{
    public function GetEntityDataClass($HlBlockId) {
        if (empty($HlBlockId) || $HlBlockId < 1)
        {
            return false;
        }
        $hlblock = HLBT::getById($HlBlockId)->fetch();
        $entity = HLBT::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        return $entity_data_class;
    }
    
    public function GetHLData($HlBlockId) {
    
        $Entity = GetEntityDataClass($HlBlockId);
        $rsData = $Entity::getList(array(
            'select' => array('*')
        ));
        while($el = $rsData->fetch()){
            $arHL[] = $el;
        }
        return $arHL;
    }
    public function GetHLDataByName($Entity, $name) {
    
        $rsData = $Entity::getList(array(
            'select' => array('ID'),
            'filter' => array('UF_NAME' => $name)
        ));
        while($el = $rsData->fetch()){
            $arHL[] = $el;
        }
        return $arHL;
    }
    public function AddHLDataByName($Entity, $name) {
    
        $result = $Entity::add(array(
            'UF_NAME'         => $name
        ));
        return $result;
    }
    public function GetHLDataBy($Entity, $col,  $name) {
    
        $rsData = $Entity::getList(array(
            'select' => array('ID'),
            'filter' => array('UF_'.$col => $name)
        ));
        while($el = $rsData->fetch()){
            $arHL[] = $el;
        }
        return $arHL;
    }
    public function AddHLDataBy($Entity, $col,  $name) {
    
        $result = $Entity::add(array(
            'UF_'.$col         => $name
        ));
        return $result;
    }  
}
