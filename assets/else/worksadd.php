<?php

define ('DB_NAME', 'samba_db');
define ('DB_USER', 'root');
define ('DB_PASSWORD', 'likemelikeme456');
define ('DB_HOST', 'localhost');

$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD); 
if (!$link)
	{
		die('Could not connect: ' . mysql_error()); 
	} 
	$db_selected = mysql_select_db(DB_NAME, $link); 
if (!$db_selected) 
	{
		die('Cant use ' . DB_NAME . ': ' . mysql_error());
	}     
echo 'Connected Successfully'; mysql_close();
	$WorName = ($_POST['WorName']);
	$WorDone = ($_POST['WorDone']);
	$WorEpisodes = $_POST['WorEpisodes'];
	$WorLang = $_POST['WorLang'];
	$WorPriority = $_POST['WorPriority']; 
	$WorFull = $_POST['WorFull'];
	$WorChannels = $_POST['WorChannels']; 
	$WorImg = $_POST['WorImg']; 
	$WorBrief = $_POST['WorBrief'];
	$WorType = $_POST['WorType']; 	
$con = mysql_connect('localhost', 'root', 'likemelikeme456') or die(mysql_error());
$sql = "INSERT INTO Works (WorName,WorDone,WorEpisodes,WorLang,WorPriority,WorFull,WorChannels,WorImg,WorBrief,WorType)
					VALUES ('$WorName','$WorDone','$WorEpisodes','$WorLang','$WorPriority','$WorFull','$WorChannels','$WorImg','$WorBrief','$WorType')";
if (!mysql_query($sql))
	{
		die ('Error: '.mysql_error());
	}
else
	{
		echo 'Work Added';
	}
mysql_close();
?>
