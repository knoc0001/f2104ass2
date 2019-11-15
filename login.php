<?php
ob_start();
session_start();
?>
<html>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
<link href="css/styles.css" rel="stylesheet">

<head>
    <title>Login</title>
</head>

<nav class="navbar navbar-expand-md navbar-light fixed-top" style="background-color: #21468F;">

        <a class="navbar-brand text-white" href="index.html">Famox</a>

        <button class="navbar-toggler" type="button" data-toggle="collapse"
        data-target="#navbarText" aria-controls="navbarText" aria-expanded="false"
        aria-label="Toggle navigation">
            <span class="navbar-toggler-icon text-white"></span>
        </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarText">

    <ul class="navbar-nav">
            <li><a class="nav-link text-white" href="displayproductrec.php">Products</a></li>
            <li><a class="nav-link text-white" href="displayclient.php">Clients</a></li>
            <li><a class="nav-link text-white" href="displaycategory.php">Categories</a></li>
            <li><a class="nav-link text-white" href="displayproject.php">Projects</a></li>
            <li><a class="nav-link text-white" href="displayimages.php?Action=Delete">Images</a></li>
            <li><a class="nav-link text-white" href="documentation.php">Documentation</a> </li>

            <li>
            <a class="nav-link text-white" href="emptypage.php"><img src="css/cart.png" class="img-fluid" alt="Responsive image"></a>
            </li>
    </ul>
    </div>
</nav>

</br></br></br></br>

<body>
<?php
require_once 'connection.php';
$strAction = $_GET['Action'];

switch ($strAction)
{
    case 'Login':
?>
        <h1 style="color: white;">Login</h1>
        <form method="post" action="login.php?Action=ConfirmLogin" style="color: white;">
            Email: <input type="text" name="email" size="20">
            Password: <input type="password" name="password" size="20">
            <input type="submit" value="Login" OnClick="window.location='Login.php?Action=ConfirmLogin">
            <input type="reset" value="Reset">
        </form>
<?php
        break;

    case 'ConfirmLogin':
        $_SESSION['email'] = $_POST['email'];
        $stmt = $dbh -> prepare("SELECT user_email, user_password FROM user WHERE user_email=? AND 
            user_password=?");
        $hashed_pword = hash('sha256', $_POST['password']);
        $stmt -> execute([$_POST['email'], $hashed_pword]);
        $result = $stmt -> fetchObject();
        if(!empty($result)) {
            $_SESSION['loggedin'] = true;
            header("Location: index.html");
        }
        else {
            ?>
            <body>
            <head>
                <title>Incorrect login details</title>
            </head>
            <body
            <p style="color: white;">Incorrect login details, redirecting back to login page</p>
            <p style="color: white;">Click <a style="color: #21468F;" href="login.php?Action=Login">here</a> if not redirected</p>
            </body>
            </html>
        <?php
            header("refresh:3, url=login.php?Action=Login");
        }
        break;
}
?>
</body>

</html>
