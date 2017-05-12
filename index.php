<?php

define('IS_GET',        $_SERVER['REQUEST_METHOD'] =='GET' ? true : false);
define('IS_POST',       $_SERVER['REQUEST_METHOD'] =='POST' ? true : false);
define('IS_AJAX',       ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) ? true : false);
$iweb = dirname(__FILE__)."/lib/iweb.php";
$config = dirname(__FILE__)."/config/config.php";
require($iweb);
IWeb::createWebApp($config)->run();
?>