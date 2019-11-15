<html>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
<link href="css/styles.css" rel="stylesheet">

<head>
    <meta name="viewport" content="width-device-width, initial-scale=1">
</head>

<body>

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

<head>
    <title>Client</title>
</head>

<h1 style="color: white;">Client</h1>

<input type="button" value='Add a client' OnClick='window.location="AddClient.php?Action=Add"'>
<input type='button' value='Send Email' OnClick='window.location="sendEmail.php"'>
<input type='button' value='Generate Client PDF' OnClick='window.location="ClientPDF.php"'>

<?php
ob_start();
session_start();
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    header("Location: login.php?Action=Login");
}

require_once "connection.php";
$query = "SELECT * FROM user WHERE user_role= ?";
$stmt = $dbh->prepare($query);
if (!$stmt->execute(['C'])){
    ?>
    <title>Error occurred</title>
    <center>
        <h2>Error retrieving client details</h2>
        <p>An error occurred while trying to retrieve the client details</p>
    </center>
    <?php
}
?>
<table class="table table-striped table-hover" style="background-color: white;">
    <th>ID</th>
    <th>Name</th>
    <th colspan="2">Options</th>
    <?php
if ($stmt -> rowCount() > 0) {
    while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        echo '<tr><td>'.
            $row['user_id'].'</td><td>'.
            '<a href=ViewClient.php?clientno='. $row['user_id']. '>' . $row['user_fname'] . ' ' . $row['user_lname'] .
            '</a></td><td>'.
            '<a href=UserModify.php?clientno='. $row['user_id']. '&Action=Update>Update</a></td><td>'.
            '<a href=UserModify.php?clientno='. $row['user_id']. '&Action=Delete>Delete</a></td></tr>';
    }
}
echo '</table>';


$file = "displayclient.php";
$add = "addClient.php";
$modify = "UserModify.php";
$view = "ViewClient.php";
echo "<center><a href='DisplaySource.php?filename=".$file."&addname=".$add."&modifyname=".$modify."&viewname=".$view."' target='_blank'> <img src = 'phpPrint/client.png' class='img-thumbnail' width='200' height='40'> </a></center>";

echo "<br><center></center><h5 style='color: white;'>Note: it was unclear as to whether the client pdf and email files needed to have their source codes displayed as well but they have been compiled them below in case of necessity.</h5></center>";
$pdfLink = "ClientPDf.php";
echo "<center><button><a href='DisplaySource2.php?filename=".$pdfLink."' target='_blank'> Client PDF </a></button></center>";

$emailLink = "sendEmail.php";
echo "<center><button><a href='DisplaySource2.php?filename=".$emailLink."' target='_blank'> Send Email </a></button></center>";

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>

</html>