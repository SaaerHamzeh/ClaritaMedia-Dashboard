<?php
define('DOC_ROOT', realpath(dirname(__FILE__) . '/./'));
function my_autoloader($class_name)
{
    require DOC_ROOT . '/classes/' . strtolower($class_name) . '.php';
}
spl_autoload_register('my_autoloader');

$db = Database::getDatabase();
session_start();

$_SESSION = array();

session_destroy();

header('Location: admin_login.php');
exit();
