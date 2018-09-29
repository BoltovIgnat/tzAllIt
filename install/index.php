<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use BoltovIgnat\ParseCsv\ExampleTable;
use BoltovIgnat\ParseCsv\BoltovFunction;

Loc::loadMessages(__FILE__);

if (class_exists('boltovignat_parseCsv')) {
    return;
}

class boltovignat_parseCsv extends CModule
{
    /** @var string */
    public $MODULE_ID;

    /** @var string */
    public $MODULE_VERSION;

    /** @var string */
    public $MODULE_VERSION_DATE;

    /** @var string */
    public $MODULE_NAME;

    /** @var string */
    public $MODULE_DESCRIPTION;

    /** @var string */
    public $MODULE_GROUP_RIGHTS;

    /** @var string */
    public $PARTNER_NAME;

    /** @var string */
    public $PARTNER_URI;

    public function __construct()
    {
        $this->MODULE_ID = 'boltovignat.parseCsv';
        $this->MODULE_VERSION = '0.0.1';
        $this->MODULE_VERSION_DATE = '2018-09-29 01:01:01';
        $this->MODULE_NAME = Loc::getMessage('MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = "BoltovIgnat";
        $this->PARTNER_URI = "http://www.boltovignat.ru";
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->installDB();
    }

    public function doUninstall()
    {
        $this->uninstallDB();
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

    public function installDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            ExampleTable::getEntity()->createDbTable();
        }
    }

    public function uninstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            $connection = Application::getInstance()->getConnection();
            $connection->dropTable(ExampleTable::getTableName());
        }
    }
}
