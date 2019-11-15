<?php
session_start();

if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    header("Location: login.php?Action=Login");
}

require_once 'connection.php';
require_once 'CommitAndRollback.php';
?>
<html>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
<link href="css/styles.css" rel="stylesheet">

<head>
    <meta name="viewport" content="width-device-width, initial-scale=1">
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
            <li><a class="nav-link text-white" href="documentation.php">Documentation</a></li>

            <li>
                <a class="nav-link text-white" href="emptypage.php"><img src="css/cart.png" class="img-fluid" alt="Responsive image"></a>
            </li>
        </ul>
    </div>
</nav>

</br></br></br></br>
<?php
$curr_user = $_GET['clientno'];
$query = "SELECT * FROM user WHERE user_id =?";
$stmt = $dbh -> prepare($query);
if (!$stmt -> execute([$curr_user])){
    $str =" <h2>Error retrieving client details</h2><p>An error occurred while trying to retrieve the client 
 details</p>";
    commitOrRollback($dbh,'N',$str,"displayclient.php");
    exit();
}
$user = $stmt -> fetchObject();
?>
<head>
    <title><?php echo ($user -> user_fname).' '.($user -> user_lname);?>></title>
</head>
<body>
<div class="container jumbotron">
    <h1><?php echo ($user -> user_fname).' '.($user -> user_lname);?></h1>
    <br>
    <p><b>User ID: </b><?php echo $user -> user_id;?></p>
    <p><b>Email: </b><?php echo $user -> user_email;?></p>
    <p><b>Address: </b><?php echo $user -> user_address;?></p>
    <p><b>Mobile number: </b><?php
        if ($user -> user_mobile){
            echo $user -> user_mobile;
        } else{
            echo 'N/A';
        }?></p>
    <p><b>Phone number: </b><?php
        if ($user -> user_phone){
            echo $user -> user_phone;
        } else{
            echo 'N/A';
        }?></p>
    <p><b>Subscribed to mailing list: </b><?php
        if (($user -> user_maillist) == 'Y'){
            echo 'Yes';
        } else {
            echo 'No';
        };?></p>
</div>

<input type="button" value="Return to the previous page" onclick="history.back()">

<?php

$file = "displayclient.php";
$add = "addClient.php";
$modify = "UserModify.php";
$view = "ViewClient.php";
echo "<center><a href='DisplaySource.php?filename=".$file."&addname=".$add."&modifyname=".$modify."&viewname=".$view."' target='_blank'> <img src = 'phpPrint/client.png' class='img-thumbnail' width='200' height='40'> </a></center>";

?>
</body>
</html>
