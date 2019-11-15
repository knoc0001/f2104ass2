<?php
ob_start();
session_start();
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    header("Location: login.php?Action=Login");
}
require_once "vendor/autoload.php";
require_once "connection.php";

$pdf = new TCPDF('L', 'mm', 'A4', true);
$pdf->SetMargins(10, 20, 10, true);
$pdf->SetFont('helvetica', '', 11);
$pdf->AddPage();
$content = '<h1>Famox Client Details</h1><br> 
<table border="1" cellpadding="3">
    <tr>  
        <th width="5%">ID</th>  
        <th width="10%">First name</th>  
        <th width="10%">Last name</th>  
        <th width="30%">Address</th>  
        <th width="25%">Email</th>  
        <th width="10%">Mobile number</th>  
        <th width="10%">Phone number</th>  
    </tr>  
';
$query = "SELECT * FROM user WHERE user_role= ? ORDER BY user_lname, user_fname";
$stmt = $dbh->prepare($query);
if (!$stmt->execute(['C'])){
    ?>
    <html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <title>Error occurred</title>
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
    <center><h2>Error retrieving client details</h2>
        <p>An error occurred while trying to retrieve the client details</p>
        <input type='button' value='Return to List' OnClick='window.location="displayclient.php"'></center>
    </center>
    </body>
    <?php
    exit();
}

$output = '';
while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
    $content .= '<tr>  
    <td>'.$row["user_id"].'</td>  
    <td>'.$row["user_fname"].'</td>  
    <td>'.$row["user_lname"].'</td>  
    <td>'.$row["user_address"].'</td>  
    <td>'.$row["user_email"].'</td>  
    <td>'.$row["user_mobile"].'</td>  
    <td>'.$row["user_phone"].'</td>  
    </tr>';
}
$content .= '</table>';
$pdf->writeHTML($content);
$pdf->Output('clientdetails.pdf', 'I');

?>