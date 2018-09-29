<?php
define('ADMIN_MODULE_NAME', 'boltovignat.parseCsv');

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

// @todo: Здесь - какой-то системный код, читающие данные и всё такое

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';

echo "Welcome to admin area <br>";
echo '<a href="/bitrix/admin/ibUploadProducts.php" class="adm-btn adm-btn-add" title="Загрузить товары" id="btn_new">Загрузить товары</a>';
?>


<?require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
