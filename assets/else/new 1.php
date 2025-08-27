<?php		
echo "Hii"."</br>";
$Action = isset($_POST['Action'])?$_POST['Action']:"11";
echo "Action: $Action"."</br>";
$WorNameErr = $WorDonelErr = $WorLanguageErr = $WorPriorityErr = $WorTypeErr = $WorImgErr = "";
$WorName = $WorDone = $WorEpisodes = $WorLanguage = $WorPriority = $WorFull = $WorChannels = $WorImg = $WorBrief = $WorType = "";

if ( $Action=='Add' )
{
	$WorName = ($_POST['WorName']);
	$WorDone = ($_POST['WorDone']);
	$WorEpisodes = $_POST['WorEpisodes'];
	$WorLanguage = $_POST['WorLanguage'];
	$WorPriority = $_POST['WorPriority']; 
	$WorFull = $_POST['WorFull'];
	$WorChannels = $_POST['WorChannels']; 
	$WorImg = $_POST['WorImg']; 
	$WorBrief = $_POST['WorBrief'];
	$WorType = $_POST['WorType']; 	

	if (!$WorName )
		$WorNameErr = ('يرجى إدخال اسم العمل');
	elseif (!$WorDone )
		$WorDonelErr = ('يرجى إدخال ما أنجزناه في العمل');
	elseif ( !$WorLanguage )
		$WorLanguageErr =('يرجى إدخال لغة العمل');
	elseif ( !$WorType )
		$WorTypeErr =('يرجى اختيار نوع العمل');
	elseif ( !$WorPriority )
		$WorPriorityErr =('يرجى إدخال أولوية العمل');
	elseif ( !$WorImg )
		$WorImgErr =('يرجى إدخال صورة العمل');
	else
	{
		mysqli_query($connect,"INSERT INTO Works (WorName,WorDone,WorEpisodes,WorLanguage,WorPriority,WorFull,WorChannels,WorImg,WorBrief,WorType)
		        VALUES ('$WorName','$WorDone','$WorEpisodes','$WorLanguage','$WorPriority','$WorFull','$WorChannels','$WorImg','$WorBrief','$WorType)");
				
		if(mysqli_affected_rows($connect) > 0)
			{
				echo "Work Added";
			} 
		else 
			{
				echo "Work NOT Added<br>";
				echo mysqli_error ($connect);
			}
	}
}
?>