<?php
// Document Root to be used in includes anywhere
define('DOC_ROOT', realpath(dirname(__FILE__) . '/./'));

// Include functions library
require DOC_ROOT . '/functions.php'; // magical __autoload() defined in this file

date_default_timezone_set('Europe/Helsinki');

// Declare configuration variables as early as possible, then load db config settings into constants
$db = Database::getDatabase();

?>