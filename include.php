<?php
/*defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;

Loader::registerAutoLoadClasses('boltovignat.parseCsv', array(
    'BoltovIgnat\ParseCsv\ExampleTable' => 'lib/ExampleTable.php',
    'BoltovIgnat\ParseCsv\BoltovFunction' => 'lib/BoltovFunction.php',
));

EventManager::getInstance()->addEventHandler('main', 'OnAfterUserAdd', function(){
    // do something when new user added
});
