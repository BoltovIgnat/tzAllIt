<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$aMenu = array(
    array(
        'parent_menu' => 'global_menu_content',
        'sort' => 400,
        'text' => "Модуль тестовго задания",
        'title' => "Модуль тестовго задания",
        'url' => 'ibParseCsvIndex.php',
        'items_id' => 'menu_references',
        'items' => array(
            array(
                'text' => "Парсинг Csv",
                'url' => 'ibParseCsvIndex.php',
                'more_url' => array('ibParseCsvIndex.php'),
                'title' => "Уруруру",
            ),
        ),
    ),
);

return $aMenu;
