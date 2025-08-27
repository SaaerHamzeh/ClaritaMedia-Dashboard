<?php
define('DOC_ROOT', realpath(dirname(__FILE__) . '/./'));
function my_autoloader($class_name)
{
    require DOC_ROOT . '/classes/' . strtolower($class_name) . '.php';
}
spl_autoload_register('my_autoloader');

$db = Database::getDatabase();

session_start();
if (isset($_SESSION["login"])) {
    header("location: clarita-dashboard.php");
    exit();
}

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT Admin_id, Admin_password FROM admin WHERE Admin_email = :email";
    $result = $db->query($query, ['email' => $email]);

    if ($db->hasRows($result)) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row["Admin_password"])) {
            $_SESSION["login"] = true;
            $_SESSION["id"] = $row["Admin_id"];
            header("location: clarita-dashboard.php");
            exit();
        } else {
            echo "<script>alert('The password you entered does not match.');</script>";
        }
    } else {
        echo "<script>alert('The email you entered is not registered.');</script>";
        header("location: admin_signup.php");
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin login</title>
    <link rel="shortcut icon" href="assets/ico/favicon.png">
    <link rel="stylesheet" href="./dashboard_style/registeration.css">
    <link rel="stylesheet" href="./dashboard_style/dashboard-css.css">
    <!-- _____________________ -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/elegant-font/code/style.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/flexslider/flexslider.css">
    <link rel="stylesheet" href="assets/css/form-elements.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- _____________________ -->
</head>

<body>
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
            <div class="collapse navbar-collapse" id="top-navbar-1">
                <span id="header-name" style="width:auto;">Admin Registration</span>
            </div>
        </div>
    </nav>
    <div class="main">
        <div class="section">
            <div class="signup-container">
                <h2>Log-in</h2>
                <form action="" method="post">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="submit">Log-in</button>
                    <hr id="hr">
                    <span class="register_span">Click on this button below if you are a new ADMIN here</span>
                    <a href="./admin_signup.php" class="main-button">sign-up</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>