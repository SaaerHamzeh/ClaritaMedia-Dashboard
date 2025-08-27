<?php
$ImgID = intval($_GET['ImgID']);
define('DOC_ROOT', realpath(dirname(__FILE__) . '/./'));
function my_autoloader($class_name) {
    require DOC_ROOT.'/classes/'.strtolower($class_name).'.php';
}

spl_autoload_register('my_autoloader');

$db = Database::getDatabase();
$photo = $db->getValue("SELECT WorImg FROM Works WHERE WorID='$ImgID'");

header("Content-type: image/jpeg");
echo $photo;
