<?php
define('DOC_ROOT', realpath(dirname(__FILE__) . '/./'));
function my_autoloader($class_name)
{
    require DOC_ROOT . '/classes/' . strtolower($class_name) . '.php';
}
spl_autoload_register('my_autoloader');

$db = Database::getDatabase();
// _________________________________________________
session_start();
if (isset($_SESSION["login"])) {
    header("location: clarita-dashboard.php");
    exit();
}

if (isset($_POST['submit'])) {
    $fullname = $_POST['fullname'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $confirmpassword = trim($_POST['confirmpassword']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format');</script>";
    } elseif ($confirmpassword !== $password) {
        echo "<script>alert('Password does not match');</script>";
    } else {
        $duplicate = $db->query("SELECT * FROM admin WHERE Admin_email = :email", array('email' => $email));

        if ($db->numRows($duplicate) > 0) {
            echo "<script>alert('E-mail has already been taken');</script>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $db->query("INSERT INTO admin (Admin_fullname, Admin_email, Admin_password) 
            VALUES (:fullname, :email, :password)", array('fullname' => $fullname, 'email' => $email, 'password' => $hashed_password));
            echo "<script>alert('Registration successful');</script>";
            header("location: admin_login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin signup</title>
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
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="top-navbar-1">
                <span id="header-name" style="width:auto;">Admin Registration</span>
            </div>
        </div>
    </nav>
    <div class="main">
        <div class="section">
            <div class="signup-container">
                <h2>Sign Up</h2>
                <form action="" method="post">
                    <input type="text" name="fullname" placeholder="Full Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirmpassword" placeholder="Re-enter Password" required>
                    <button type="submit" name="submit">Sign Up</button>
                    <hr id="hr">
                    <span class="register_span">If you already registered your information, Click on login button below </span>
                    <a href="./admin_login.php" class="main-button">log-in</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>