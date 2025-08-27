<?php
$type = isset($_GET['t']) ? htmlspecialchars($_GET['t']) : 'D';
$page = isset($_GET['p']) ? intval($_GET['p']) : 1;

define('DOC_ROOT', realpath(dirname(__FILE__) . '/./'));

function my_autoloader($class_name)
{
    require DOC_ROOT . '/classes/' . strtolower($class_name) . '.php';
}
spl_autoload_register('my_autoloader');

$db = Database::getDatabase();

session_start();

// Check if admin is logged in
if (!isset($_SESSION["login"]) || !isset($_SESSION["id"])) {
    header("location: admin_login.php");
    exit();
}

// Retrieve admin information from the database
$id = intval($_SESSION["id"]);
$result = $db->query("SELECT * FROM admin WHERE Admin_id = :id", array('id' => $id));

if ($db->numRows($result) > 0) {
    $row = $db->getRow($result);
} else {
    // Handle case where admin data is not found
    echo "<script>alert('admin not found')</script>";
    // session_destroy();
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CLARITA Media Dashboard</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/elegant-font/code/style.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/flexslider/flexslider.css">
    <link rel="stylesheet" href="assets/css/form-elements.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/media-queries.css">
    <link rel="stylesheet" href="./dashboard_style/dashboard-css.css">
    <link rel="stylesheet" href="./dashboard-forms.css">
    <!-- ___________________________________________________ -->
    <link rel="shortcut icon" href="assets/ico/favicon.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">
</head>

<body>
    <!-- Top menu -->
    <nav class="navbar" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#top-navbar-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand"></a>

            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="top-navbar-1">
                <span id="header-name">Clarita-Media DASHBOARD</span>
                <ul class="nav navbar-nav navbar-right">
                    <!-- <li <?php if (preg_match('/clarita-dashboard.php/', $_SERVER["PHP_SELF"])) echo ' class="active"'; ?>>
                        <a href="works.php"><span aria-hidden="true" class="icon_archive"></span><br>Works</a>
                    </li> -->
                    <div id="admin-logout">
                        <a href="admin_logout.php">
                            <i class="fa fa-sign-out-alt icon"></i>
                            <span id="logout">LogOut</span>
                        </a>

                    </div>
                </ul>

            </div>
        </div>
    </nav>

    <div class="portfolio-container">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <!-- ____________________________________pop_up_form____________________________________ -->
                    <div class="btn">
                        <button class="showFormBtn" data-form="form1-adding">Add Work</button>
                        <button class="showFormBtn" data-form="form2-modifying">Modify Work</button>
                        <button class="showFormBtn" data-form="form3-deleting">Delete Work</button>
                    </div>
                    <!-- ___________________________________________________________________________________ -->
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-12 ">
                    <a href="clarita-dashboard.php?t=D" class="btn btn-primary<?php if ($type == "D") echo " active"; ?>">Dubbing</a>
                    <a href="clarita-dashboard.php?t=S" class="btn btn-primary<?php if ($type == "S") echo " active"; ?>">Subtitling</a>
                    <a href="clarita-dashboard.php?t=T" class="btn btn-primary<?php if ($type == "T") echo " active"; ?>">Translation</a>
                </div>
            </div>
            <div class="row">
                <!-- _______________pop_up_empty_div____________ -->
                <div id="overlay"></div>
                <!-- _______________pop_up_empty_div____________ -->

                <?php
                if ($type == 'T')
                    $where = "WHERE (WorDone='TD' OR WorDone='T')";
                else
                    $where = "WHERE WorDone='$type'";

                $db = Database::getDatabase();
                //WorID,WorName,WorPriority,WorEpisodes,WorLang,WorType,WorDone,WorFull,WorBrief,WorChannels,WorImg 
                $num_records = $db->getValue("SELECT COUNT(WorID) FROM Works $where");
                $per_page = 16;
                $pager = new Pager($page, $per_page, $num_records);
                $rows = $db->getRows("SELECT WorID,WorName,WorPriority,WorEpisodes,WorLang,WorType,WorDone,WorBrief,WorChannels FROM Works $where ORDER BY WorPriority DESC LIMIT {$pager->firstRecord},$per_page");
                foreach ($rows as $row) {
                    if ($row['WorDone'] == 'S') {
                        $title = "Subtitling";
                    } elseif ($row['WorDone'] == 'TD')
                        $title = "Translating and Writing the dubbing dialogue";
                    elseif ($row['WorDone'] == 'D') {
                        $title = "Dubbing";
                    }
                    if ($row['WorDone'] == 'T') {
                        $title = "Translation";
                    }
                    $desc = $row['WorName'] . "| pri: " . $row['WorPriority'] . " <br/> " . $row['WorLang'] . " " . $row['WorType'];
                    if ($row['WorType'] != "movie")
                        $desc .= " (" . $row['WorEpisodes'] . " episodes)";
                    if ($row['WorChannels'])
                        if (strlen($row['WorChannels']))
                            $desc .= "<br>shown on " . $row['WorChannels']


                ?>
                    <div class="col-sm-3">
                        <div class="team-box wow fadeInUp">
                            <img src="workimg.php?ImgID=<?php echo $row['WorID'] ?>" alt="" data-at2x="workimg.php?ImgID=<?php echo $row['WorID'] ?>">
                            <div class="team-social">
                                <h3><?php echo $title . '<br>' ?></h3>
                                <?php echo $desc ?>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <!-- ________________________________________add_product__________________________________________ -->
        <div class="col-3 popupForm" id="form1-adding">
            <form id="addSubtitlingForm" action="./CRUD-queries.php" method="post" enctype="multipart/form-data">
                <h2>Add a Work</h2>
                <div class="crud-form">
                    <div class="left-side-form">
                        <div class="form-group">
                            <label for="addWorkDone">Work Done: <i class="fas fa-map-marker-alt icon"></i></label>
                            <input type="radio" id="addWorkDoneDubbing" value="D" name="WorkDone" required> Dubbing _
                            <input type="radio" id="addWorkDoneSubtitling" value="S" name="WorkDone" required> Subtitling <br>
                            <input type="radio" id="addWorkDoneTranslation" value="T" name="WorkDone" required> Translation _
                            <input type="radio" id="addWorkDoneTD" value="TD" name="WorkDone" required> Translation & Dubbing
                        </div>
                        <div class="form-group">
                            <label for="addWorkName">Work Name:</label>
                            <input type="text" id="addWorkName" name="WorkName" required>
                        </div>
                        <div class="form-group">
                            <label for="addWorkPriority">Work Priority:</label>
                            <input type="number" id="addWorkPriority" name="WorkPriority" required>
                        </div>
                        <div class="form-group">
                            <label for="addWorkEpisodes">Work Episodes: <i class="fas fa-map-marker-alt icon"></i></label>
                            <input type="number" id="addWorkEpisodes" name="WorkEpisodes" required>
                        </div>
                        <div class="form-group">
                            <label for="addWorkLanguage">Work language: <i class="fas fa-map-marker-alt icon"></i></label>
                            <input type="text" id="addWorkLanguage" name="WorkLanguage" required>
                        </div>
                    </div>
                    <div class="right-side-form">
                        <div class="form-group">
                            <label for="addWorkType">Work Type: </label>
                            <input type="radio" id="addWorkTypeSeries" value="Series" name="WorkTypeAdd" required> Series _
                            <input type="radio" id="addWorkTypeMovie" value="Movie" name="WorkTypeAdd" required> Movie <br>
                            <input type="radio" id="addWorkTypeShow" value="Show" name="WorkTypeAdd" required> Show _
                            <input type="radio" id="addWorkTypeDocumentary" value="Documentary" name="WorkTypeAdd" required> Documentary
                        </div>
                        <div class="form-group">
                            <label for="addWorkFull">Work Full: </label>
                            <input type="radio" id="addWorkFullFull" value="Full" name="WorkFull" required> Full
                            <input type="radio" id="addWorkFullPartial" value="Partial" name="WorkFull" required> Partial
                        </div>
                        <div class="form-group">
                            <label for="addWorkBrief">Work Brief: </label>
                            <input type="text" id="addWorkBrief" name="WorkBrief">
                        </div>
                        <div class="form-group">
                            <label for="addWorkChannels">Work Channels: </label>
                            <input type="text" id="addWorkChannels" name="WorkChannels" required>
                        </div>
                        <div class="form-group">
                            <label for="addWorkImage">Work Image: </label>
                            <input type="file" id="addWorkImage" name="WorkImage">
                        </div>
                    </div>
                </div>
                <button type="submit" name="Add-product">Add Work</button>
            </form>
            <script>
                const workEpisodesInput = document.getElementById('addWorkEpisodes');
                const workTypeMovie = document.getElementById('addWorkTypeMovie');


                document.querySelectorAll('input[name="WorkTypeAdd"]').forEach((radio) => {
                    radio.addEventListener('change', function() {
                        if (workTypeMovie.checked) {

                            workEpisodesInput.value = 0;
                            workEpisodesInput.disabled = true;
                        } else {
                            workEpisodesInput.value = null;
                            workEpisodesInput.disabled = false;
                        }
                    });
                });
            </script>
        </div>
        <!-- ________________________________________modify_product__________________________________________ -->
        <div class="col-3 popupForm" id="form2-modifying">
            <form id="modifyworkForm" action="./CRUD-queries.php" method="post" enctype="multipart/form-data">
                <h2>Modify a Work</h2>
                <div class="crud-form">
                    <div class="left-side-form">
                        <div class="form-group">
                            <label for="workTypeSelect">Select work Done:</label>
                            <select id="workTypeSelect" style="margin-bottom: 10px;" name="workTypeSelect">
                                <option value="">--Select Work Done--</option>
                                <option value="D">Dubbing</option>
                                <option value="S">Subtitling</option>
                                <option value="T">Translation</option>
                            </select>
                            <select id="modifyworkIdubbing" name="WorkID" style="display:none;">
                                <option value="">( Dubbing works )</option>
                                <?php
                                $query = "SELECT WorID, WorName FROM Works WHERE WorDone='D'";
                                $products = $db->getRows($query);
                                foreach ($products as $product) : ?>
                                    <option value="<?= $product['WorID'] ?>"><?= $product['WorName'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select id="modifyworkIdsubtitling" name="WorkID" style="display:none;">
                                <option value="">( Subtitling works )</option>
                                <?php
                                $query = "SELECT WorID, WorName FROM Works WHERE WorDone='S'";
                                $products = $db->getRows($query);
                                foreach ($products as $product) : ?>
                                    <option value="<?= $product['WorID'] ?>"><?= $product['WorName'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select id="modifyworkIdtranslation" name="WorkID" style="display:none;">
                                <option value="">( Translation works )</option>
                                <?php
                                $query = "SELECT WorID, WorName FROM Works WHERE WorDone='T' or WorDone='TD'";
                                $products = $db->getRows($query);
                                foreach ($products as $product) : ?>
                                    <option value="<?= $product['WorID'] ?>"><?= $product['WorName'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="modifyWorkDone">Work Done:</label>
                            <input type="radio" id="modifyWorkDoneDubbing" value="D" name="WorkDone" required> Dubbing _
                            <input type="radio" id="modifyWorkDoneSubtitling" value="S" name="WorkDone" required> Subtitling <br>
                            <input type="radio" id="modifyWorkDoneTranslation" value="T" name="WorkDone" required> Translation _
                            <input type="radio" id="modifyWorkDoneTD" value="TD" name="WorkDone" required> Translation & Dubbing
                        </div>
                        <div class="form-group">
                            <label for="modifyWorkName">Work Name:</label>
                            <input type="text" id="modifyWorkName" name="WorkName" required>
                        </div>
                        <div class="form-group">
                            <label for="modifyWorkPriority">Work Priority:</label>
                            <input type="number" id="modifyWorkPriority" name="WorkPriority" required>
                            <span id="priorityWarning" style="color: red; display: none;"></span>
                        </div>
                        <div class="form-group">
                            <label for="modifyWorkEpisodes">Work Episodes:</label>
                            <input type="number" id="modifyWorkEpisodes" name="WorkEpisodes" required>
                        </div>
                        <div class="form-group">
                            <label for="modifyWorkLanguage">Work language:</label>
                            <input type="text" id="modifyWorkLanguage" name="WorkLanguage">
                        </div>
                    </div>
                    <div class="right-side-form">
                        <div class="form-group">
                            <label for="modifyWorkType">Work Type: </label>
                            <input type="radio" id="modifyWorkTypeSeries" value="series" name="WorkTypeEdit" required> Series _
                            <input type="radio" id="modifyWorkTypeMovie" value="movie" name="WorkTypeEdit" required> Movie <br>
                            <input type="radio" id="modifyWorkTypeShow" value="show" name="WorkTypeEdit" required> Show _
                            <input type="radio" id="modifyWorkTypeDocumentary" value="documentary" name="WorkTypeEdit" required> Documentary
                        </div>
                        <div class="form-group">
                            <label for="modifyWorkFull">Work Full: </label>
                            <input type="radio" id="modifyWorkFullFull" value="Full" name="WorkFull" required> Full
                            <input type="radio" id="modifyWorkFullPartial" value="Partial" name="WorkFull" required> Partial
                        </div>
                        <div class="form-group">
                            <label for="modifyWorkBrief">Work Brief: </label>
                            <input type="text" id="modifyWorkBrief" name="WorkBrief">
                        </div>
                        <div class="form-group">
                            <label for="modifyWorkChannels">Work Channels: </label>
                            <input type="text" id="modifyWorkChannels" name="WorkChannels">
                        </div>
                        <div class="form-group">
                            <label for="modifyWorkImage">Work Image: </label>
                            <input type="file" id="modifyWorkImage" name="WorkImage">
                            <img id="modifyWorkImagePreview" src="" alt="Work Image Preview" style="max-width: 200px; max-height: 150px; margin-top: 10px;">
                        </div>
                    </div>
                </div>
                <button type="submit" name="Modify-product">Modify Work</button>
            </form>
            <script>
                // Event listener for work type selection (Dubbing, Subtitling, Translation)
                document.getElementById('workTypeSelect').addEventListener('change', function() {
                    var work_done = this.value;

                    // Hide all work type sections initially
                    document.getElementById('modifyworkIdubbing').style.display = 'none';
                    document.getElementById('modifyworkIdsubtitling').style.display = 'none';
                    document.getElementById('modifyworkIdtranslation').style.display = 'none';

                    // Show the selected work type section
                    if (work_done === 'D') {
                        document.getElementById('modifyworkIdubbing').style.display = 'block';
                    } else if (work_done === 'S') {
                        document.getElementById('modifyworkIdsubtitling').style.display = 'block';
                    } else if (work_done === 'T') {
                        document.getElementById('modifyworkIdtranslation').style.display = 'block';
                    }
                });

                // Event listener for fetching work details when WorkID changes
                document.querySelectorAll('select[name="WorkID"]').forEach(function(selectElement) {
                    selectElement.addEventListener('change', fetchProductDetails);
                });

                // Function to fetch product details via AJAX
                function fetchProductDetails() {
                    var workID = this.value;
                    console.log("Selected work ID:", workID);

                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "get-work-details.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            console.log("AJAX Call Completed");
                            console.log("Response Text:", xhr.responseText);
                            if (xhr.status === 200) {
                                try {
                                    var response = JSON.parse(xhr.responseText);
                                    console.log("Parsed Response:", response);

                                    if (response.error) {
                                        console.error("Error:", response.error);
                                    } else {
                                        document.querySelectorAll('input[name="WorkDone"]').forEach((input) => {
                                            if (input.value === response.WorDone) {
                                                input.checked = true;
                                            }
                                        });
                                        document.getElementById('modifyWorkName').value = response.WorName || '';
                                        document.getElementById('modifyWorkPriority').value = response.WorPriority || '';
                                        document.getElementById('modifyWorkEpisodes').value = response.WorEpisodes || '';
                                        document.getElementById('modifyWorkLanguage').value = response.WorLang || '';
                                        document.querySelectorAll('input[name="WorkTypeEdit"]').forEach((input) => {
                                            if (input.value === response.WorType) {
                                                input.checked = true;
                                            }
                                        });
                                        document.querySelectorAll('input[name="WorkFull"]').forEach((input) => {
                                            if (input.value === response.WorFull) {
                                                input.checked = true;
                                            }
                                        });
                                        document.getElementById('modifyWorkBrief').value = response.WorBrief || '';
                                        document.getElementById('modifyWorkChannels').value = response.WorChannels || '';

                                        // Update the image preview
                                        var imgPreview = document.getElementById('modifyWorkImagePreview');
                                        if (response.WorImg) {
                                            imgPreview.src = 'data:image/jpeg;base64,' + response.WorImg;
                                        } else {
                                            imgPreview.src = '';
                                        }
                                    }
                                } catch (e) {
                                    console.error("Error parsing JSON:", e);
                                }
                            } else {
                                console.error("AJAX Error:", xhr.statusText);
                            }
                        }
                    };

                    xhr.send("WorkID=" + encodeURIComponent(workID));
                }

                // Form submission handling and validation
                document.getElementById('modifyworkForm').addEventListener('submit', function(event) {
                    var workType = document.getElementById('workTypeSelect').value;
                    var workID = '';
                    if (workType === 'D') {
                        workID = document.getElementById('modifyworkIdubbing').value;
                    } else if (workType === 'S') {
                        workID = document.getElementById('modifyworkIdsubtitling').value;
                    } else if (workType === 'T') {
                        workID = document.getElementById('modifyworkIdtranslation').value;
                    }

                    if (!workID) {
                        alert('Please select a valid WorkID.');
                        event.preventDefault();
                    } else {
                        var hiddenWorkIDInput = document.createElement('input');
                        hiddenWorkIDInput.type = 'hidden';
                        hiddenWorkIDInput.name = 'WorkID';
                        hiddenWorkIDInput.value = workID;
                        this.appendChild(hiddenWorkIDInput);
                    }
                });

                // Handle "Work Episodes" input based on WorkType (e.g., set to 0 for "Movie")
                const worEpisodesInput = document.getElementById('modifyWorkEpisodes');
                const worTypeMovie = document.getElementById('modifyWorkTypeMovie');

                // Add event listener to check for "Movie" selection
                document.querySelectorAll('input[name="WorkTypeEdit"]').forEach((radio) => {
                    radio.addEventListener('change', function() {
                        if (worTypeMovie.checked) {
                            // If "Movie" is selected, set episodes to 0 and disable the input
                            worEpisodesInput.value = 0;
                            worEpisodesInput.disabled = true;
                        } else {
                            // Enable the input for other work types
                            worEpisodesInput.disabled = false;
                        }
                    });
                });
            </script>

        </div>
        <!-- ________________________________________delete_product__________________________________________ -->
        <div class="col-3 popupForm" id="form3-deleting">
            <form id="deleteproductForm" action="./CRUD-queries.php" method="post">
                <h2>Delete a Work</h2>
                <div class="crud-form">
                    <div class="left-side-form">
                        <div class="form-group">
                            <label for="DeleteworkTypeSelect">Select work type:</label>
                            <select id="DeleteworkTypeSelect" style="margin-bottom: 10px;" name="DeleteworkTypeSelect">
                                <option value="">--Select Work Type--</option>
                                <option value="D">Dubbing</option>
                                <option value="S">Subtitling</option>
                                <option value="T">Translation</option>
                            </select>
                            <select id="deleteworkIdubbing" name="WorkID" style="display:none;">
                                <option value="">( Dubbing works )</option>
                                <?php
                                $query = "SELECT WorID, WorName FROM Works WHERE WorDone='D'";
                                $products = $db->getRows($query);
                                foreach ($products as $product) : ?>
                                    <option value="<?= $product['WorID'] ?>"><?= $product['WorName'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select id="deleteworkIdsubtitling" name="WorkID" style="display:none;">
                                <option value="">( Subtitling works )</option>
                                <?php
                                $query = "SELECT WorID, WorName FROM Works WHERE WorDone='S'";
                                $products = $db->getRows($query);
                                foreach ($products as $product) : ?>
                                    <option value="<?= $product['WorID'] ?>"><?= $product['WorName'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select id="deleteworkIdtranslation" name="WorkID" style="display:none;">
                                <option value="">( Translation works )</option>
                                <?php
                                $query = "SELECT WorID, WorName FROM Works WHERE WorDone='T' or WorDone='TD'";
                                $products = $db->getRows($query);
                                foreach ($products as $product) : ?>
                                    <option value="<?= $product['WorID'] ?>"><?= $product['WorName'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="right-side-form">
                        <div class="form-group">
                            <!-- <label for="deleteWorkImage">Work Image: </label> -->
                            <img id="deleteWorkImagePreview" src="" alt="Work Image Preview" style="max-width: 230px; max-height: 160px; margin-top: 10px;">
                        </div>
                    </div>
                </div>
                <button type="submit" name="Delete-product">Delete Work</button>
            </form>
            <script>
                document.getElementById('DeleteworkTypeSelect').addEventListener('change', function() {
                    var workType = this.value;

                    document.getElementById('deleteworkIdubbing').style.display = 'none';
                    document.getElementById('deleteworkIdsubtitling').style.display = 'none';
                    document.getElementById('deleteworkIdtranslation').style.display = 'none';

                    if (workType === 'D') {
                        document.getElementById('deleteworkIdubbing').style.display = 'block';
                    } else if (workType === 'S') {
                        document.getElementById('deleteworkIdsubtitling').style.display = 'block';
                    } else if (workType === 'T') {
                        document.getElementById('deleteworkIdtranslation').style.display = 'block';
                    }

                    document.getElementById('deleteWorkImagePreview').src = ''; // Reset image preview
                });

                // Add change event listener to each workID dropdown
                var workIDDropdowns = document.querySelectorAll('select[name="WorkID"]');
                workIDDropdowns.forEach(function(dropdown) {
                    dropdown.addEventListener('change', function() {
                        var workID = this.value;

                        if (workID) {
                            // Make an AJAX request to get the image URL for the selected WorkID
                            var xhr = new XMLHttpRequest();
                            xhr.open('POST', 'get-work-details.php', true);
                            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                            xhr.onreadystatechange = function() {
                                if (xhr.readyState === 4) {
                                    if (xhr.status === 200) {
                                        var response = JSON.parse(xhr.responseText);
                                        if (response.WorImg) {
                                            var imgSrc = 'data:image/jpeg;base64,' + response.WorImg;
                                            document.getElementById('deleteWorkImagePreview').src = imgSrc;
                                        } else if (response.error) {
                                            alert('Error: ' + response.error);
                                        } else {
                                            alert('Unexpected error.');
                                        }
                                    } else {
                                        alert('Request failed. Status: ' + xhr.status);
                                    }
                                }
                            };
                            xhr.send('WorkID=' + workID);
                        } else {
                            document.getElementById('deleteWorkImagePreview').src = '';
                        }
                    });
                });

                document.getElementById('deleteproductForm').addEventListener('submit', function(event) {
                    var workType = document.getElementById('DeleteworkTypeSelect').value;
                    var workID = '';

                    if (workType === 'D') {
                        workID = document.getElementById('deleteworkIdubbing').value;
                    } else if (workType === 'S') {
                        workID = document.getElementById('deleteworkIdsubtitling').value;
                    } else if (workType === 'T') {
                        workID = document.getElementById('deleteworkIdtranslation').value;
                    }

                    if (!workID) {
                        alert('Please select a valid WorkID.');
                        event.preventDefault();
                    } else {
                        var hiddenWorkIDInput = document.createElement('input');
                        hiddenWorkIDInput.type = 'hidden';
                        hiddenWorkIDInput.name = 'WorkID';
                        hiddenWorkIDInput.value = workID;
                        this.appendChild(hiddenWorkIDInput);
                    }
                });
            </script>
        </div>
    </div>
    <!-- ___________________________________________________________________________________________________ -->
    </div>
    <?php
    if ($num_records > $per_page) {
    ?>
        <div class="call-to-action-container">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 call-to-action-text wow fadeInLeftBig">
                        <div class="call-to-action-button">
                            <a class="big-link-1" href="clarita-dashboard.php?t=<?php echo $type . "&p=1"; ?>">First</a>
                            <a class="big-link-1" href="clarita-dashboard.php?t=<?php echo $type . "&p=" . $pager->prevPage(); ?>">Prev
                        </div></a>

                        <?php
                        for ($p = 1; $p <= $pager->numPages; $p++) {
                            echo '<a class="big-link-1" href="clarita-dashboard.php?t=' . $type . '&p=' . $p . '"';
                            if ($p == $pager->page)
                                echo ' id="first"';
                            echo '>' . $p . '</a>';
                        }
                        ?>
                        <a class="big-link-1" href="clarita-dashboard.php?t=<?php echo $type . "&p=" . $pager->nextPage(); ?>">Next</a>
                        <a class="big-link-1" href="clarita-dashboard.php?t=<?php echo $type . "&p=" . $pager->numPages; ?>">Last</a>
                    </div>
                </div>
            </div>
        </div>
        </div>
    <?php
    }
    ?>

    <!-- Javascript -->
    <script src="assets/js/jquery-1.11.1.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/retina-1.1.0.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/flexslider/jquery.flexslider-min.js"></script>
    <script src="assets/js/jflickrfeed.min.js"></script>
    <script src="assets/js/masonry.pkgd.min.js"></script>
    <script src="assets/js/jquery.ui.map.min.js"></script>
    <script src="assets/js/scripts.js"></script>
    <script src="forms.js"></script>

</body>

</html>