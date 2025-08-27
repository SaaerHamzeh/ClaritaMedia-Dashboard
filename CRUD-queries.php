<?php
define('DOC_ROOT', realpath(dirname(__FILE__) . '/./'));
function my_autoloader($class_name)
{
    require DOC_ROOT . '/classes/' . strtolower($class_name) . '.php';
}
spl_autoload_register('my_autoloader');

$db = Database::getDatabase();

if (isset($_POST["Add-product"])) {
    $workname = $_POST['WorkName'];
    $workpriority = !empty($_POST['WorkPriority']) ? intval($_POST['WorkPriority']) : null;
    $workepisodes = intval($_POST['WorkEpisodes']);
    $worklanguage = $_POST['WorkLanguage'];
    $worktype = $_POST['WorkTypeAdd'];
    $workdone = $_POST['WorkDone'];
    $workfull = !empty($_POST['WorkFull']) ? $_POST['WorkFull'] : null;
    $workbrief = !empty($_POST['WorkBrief']) ? $_POST['WorkBrief'] : null;
    $workchannels = !empty($_POST['WorkChannels']) ? $_POST['WorkChannels'] : null;


    if (isset($_FILES['WorkImage']) && $_FILES['WorkImage']['error'] === UPLOAD_ERR_OK) {

        $workimage = file_get_contents($_FILES['WorkImage']['tmp_name']);
    } else {
        $workimage = null;
    }


    $stmt = $db->db->prepare("INSERT INTO Works (WorName, WorPriority, WorEpisodes, WorLang, WorType, WorDone, WorFull, WorBrief, WorChannels, WorImg) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siissssssb", $workname, $workpriority, $workepisodes, $worklanguage, $worktype, $workdone, $workfull, $workbrief, $workchannels, $null);


    if ($workimage !== null) {
        $stmt->send_long_data(9, $workimage);
    }

    if ($stmt->execute()) {
        if ($workdone === 'S') {
            header("Location: clarita-dashboard.php?t=S");
        } elseif ($workdone === 'D') {
            header("Location: clarita-dashboard.php?t=D");
        } elseif ($workdone === 'T' || $workdone === 'TD') {
            header("Location: clarita-dashboard.php?t=T");
        }
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
// _____________________________________________________Modify-product____________________________________________________________
else if (isset($_POST["Modify-product"])) {
    // error_log("Form submitted: " . print_r($_POST, true));
    // error_log("Files: " . print_r($_FILES, true));


    $workID = intval($_POST['WorkID']);
    $workname = $_POST['WorkName'];
    $workpriority = !empty($_POST['WorkPriority']) ? intval($_POST['WorkPriority']) : null;
    $workepisodes = intval($_POST['WorkEpisodes']);
    $worklanguage = $_POST['WorkLanguage'];
    $worktype = $_POST['WorkTypeEdit'];
    $workdone = $_POST['WorkDone'];
    $workfull = !empty($_POST['WorkFull']) ? $_POST['WorkFull'] : null;
    $workbrief = !empty($_POST['WorkBrief']) ? $_POST['WorkBrief'] : null;
    $workchannels = !empty($_POST['WorkChannels']) ? $_POST['WorkChannels'] : null;
    $workimage = null;
    if (isset($_FILES['WorkImage']) && $_FILES['WorkImage']['error'] === UPLOAD_ERR_OK) {
        $workimage = file_get_contents($_FILES['WorkImage']['tmp_name']);
    } else {
        $query = "SELECT WorImg FROM Works WHERE WorID = ?";
        $stmt = $db->db->prepare($query);
        if ($stmt === false) {
            error_log('Prepare failed: ' . $db->db->error);
            die('Prepare failed: ' . $db->db->error);
        }
        $stmt->bind_param('i', $workID);
        $stmt->execute();
        $stmt->bind_result($currentImage);
        $stmt->fetch();
        $stmt->close();
        $workimage = $currentImage;
    }

    if (empty($_POST['WorkID'])) {
        error_log("WorkID is missing");
        echo "Error: WorkID is missing";
        echo $workID."sfsdfsdf";
        exit();
    }

    $query = "UPDATE Works SET 
        WorName = ?,
        WorPriority = ?,
        WorEpisodes = ?,
        WorLang = ?,
        WorType = ?,
        WorDone = ?,
        WorFull = ?,
        WorBrief = ?,
        WorChannels = ?,
        WorImg = ?
        WHERE WorID = ?";

    $stmt = $db->db->prepare($query);
    if ($stmt === false) {
        error_log('Prepare failed: ' . $db->db->error);
        die('Prepare failed: ' . $db->db->error);
    }

    $stmt->bind_param(
        'siisssssssi',
        $workname,
        $workpriority,
        $workepisodes,
        $worklanguage,
        $worktype,
        $workdone,
        $workfull,
        $workbrief,
        $workchannels,
        $workimage,
        $workID
    );

    if ($stmt->execute()) {
        error_log("Query executed successfully");

        if ($workdone === 'S') {
            header("Location: clarita-dashboard.php?t=S");
        } elseif ($workdone === 'D') {
            header("Location: clarita-dashboard.php?t=D");
        } elseif ($workdone === 'T' || $workdone === 'TD') {
            header("Location: clarita-dashboard.php?t=T");
        }
        exit();
    } else {
        error_log("SQL Error: " . $stmt->error);
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// _____________________________________________________Delete-product____________________________________________________________
else if (isset($_POST["Delete-product"])) {

    $workID = $_POST['WorkID'];

    $sql = $db->query("DELETE FROM Works WHERE WorID=$workID");


    if ($sql === TRUE) {
        if ($workdone = 'S') {
            header("location: clarita-dashboard.php?t=S");
        } elseif ($workdone = 'D') {
            header("location: clarita-dashboard.php?t=D");
        } elseif ($workdone = 'T' || $workdone === 'TD') {
            header("location: clarita-dashboard.php?t=T");
        }
        echo "the record deleted successfully";
    } else {
        echo "Error: " . $sql . "<br>";
    }
}
