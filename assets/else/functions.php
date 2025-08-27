<?php
// Magic autoload function to load a class when class name mentioned
function my_autoloader($class_name) {
    require DOC_ROOT.'/classes/'.strtolower($class_name).'.php';
}

spl_autoload_register('my_autoloader');

// Some primitive error handling routines
$pageErrorArr = array();
function isErrors()
{
	global $pageErrorArr;
	if (count($pageErrorArr))
	{
		return TRUE;
	}
	return FALSE;
}
function setError($errorMsg)
{
	global $pageErrorArr;
	$pageErrorArr[] = $errorMsg;
}
function getErrors()
{
	global $pageErrorArr;
	return $pageErrorArr;
}
function outputErrors()
{
	$errors = getErrors();
	if (count($errors))
	{
		$htmlArr = array();
		foreach ($errors as $error)
		{
			$htmlArr[] = '<div class="message errormsg"><p>' . $error . '</p></div>';
		}
		return implode("\n", $htmlArr);
	}
}

// Redirects visitors to $url
function redirect($url = null)
{
	if (is_null($url))
		$url = $_SERVER['PHP_SELF'];
	header("Location: $url");
	exit();
}

//Sum the time
function time_sum($time1, $time2)
{
	$times = array($time1, $time2);
	$seconds = 0;
	foreach ($times as $time)
	{
		list($hour,$minute,$second) = explode(':', $time);
		$seconds += $hour*3600;
		$seconds += $minute*60;
		$seconds += $second;
	}
	$hours = floor($seconds/3600);
	$seconds -= $hours*3600;
	$minutes  = floor($seconds/60);
	$seconds -= $minutes*60;
	if($seconds < 9)
	{
		$seconds = "0".$seconds;
	}
	if($minutes < 9)
	{
		$minutes = "0".$minutes;
	}
	if($hours < 9)
	{
		$hours = "0".$hours;
	}
	return "{$hours}:{$minutes}:{$seconds}";
}

//validate the time
function time_valid($time)
{
	$result = true;
	list($hour,$minute,$second) = explode(':', $time);
	if ($minute>59)
		$result = false;
	if ($second>59)
		$result = false;
	return $result;
}

//MyChechDate function
function MyCheckDate($postedDate)
{
	if ( preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/[0-9]{4}$/', $postedDate) )
	{
		list($day,$month,$year) = explode('/',$postedDate);
		return checkdate($month , $day , $year);
	}
	else
	{
		return false;
	}
} 

//MyChechDate function
function MyMySQLDate($postedDate)
{
	if(!strlen($postedDate))
		return 'NULL';
	elseif ( preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/[0-9]{4}$/', $postedDate) )
	{
		list($day,$month,$year) = explode('/',$postedDate);
		return $year.'-'.$month.'-'.$day;
	}
	else
	{
		return false;
	}
} 

// Audit an operation
function audit($uid,$table,$op,$aff_id,$notes)
{
	$audit = new Audit();
	//AuditID,UserID,Time,Table,Operation,AffectedID,Notes,RemoteIp
	$audit->UserID = $uid;
	$audit->Table = $table;
	$audit->Operation = $op;
	$audit->AffectedID = $aff_id;
	$audit->Notes = $notes;
	$audit->RemoteIp = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'Not Known';
	$audit->insert();
}
?>