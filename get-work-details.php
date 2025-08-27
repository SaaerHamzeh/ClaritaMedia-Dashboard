<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['WorkID']) || empty($_POST['WorkID'])) {
        echo json_encode(["error" => "Invalid work ID"]);
        exit();
    }

    $work_id = (int)$_POST['WorkID'];

    if ($work_id <= 0) {
        echo json_encode(["error" => "Invalid work ID"]);
        exit();
    }

    define('DOC_ROOT', realpath(dirname(__FILE__) . '/./'));
    function my_autoloader($class_name)
    {
        require DOC_ROOT . '/classes/' . strtolower($class_name) . '.php';
    }
    spl_autoload_register('my_autoloader');

    try {
        $db = Database::getDatabase();

        $query = "SELECT WorName, WorPriority, WorEpisodes, WorLang, WorType, WorDone, WorFull, WorBrief, WorChannels, WorImg 
                  FROM Works WHERE WorID = ?";

        $stmt = $db->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $db->db->error);
        }

        $stmt->bind_param("i", $work_id);
        if (!$stmt->execute()) {
            throw new Exception("Error executing statement: " . $stmt->error);
        }

        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($row['WorImg']) {
                $row['WorImg'] = base64_encode($row['WorImg']); //convert the image to base64
            }
            echo json_encode($row);
        } else {
            echo json_encode(["error" => "The work not found"]);
        }

        $stmt->close();
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo json_encode(["error" => "An error occurred while fetching the details"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
