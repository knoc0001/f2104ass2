<?php
ob_start();
session_start();
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    header("Location: login.php?Action=Login");
}
?>
<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <title>Add Client</title>
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
    <?php
    require_once 'connection.php';
    require_once 'CommitAndRollback.php';

    $strAction = $_GET["Action"];

    switch($strAction)
    {
        case "Add":
            ?>
        <center><h3>Add a Client</h3>
            <p>All required fields are marked with a <span class="required">*</span></p>
        </center>
            <form method="post" action="AddClient.php?Action=ConfirmInsert">
            <table align="center">
                <tr>
                    <td><b>First Name <span class="required">*</span></b></td>
                    <td><input type="text" name="fname" size="30" value="" required
                               oninvalid="this.setCustomValidity('Please enter a first name')"
                               oninput="this.setCustomValidity('')"></td>
                </tr>
                <tr>
                    <td><b>Last Name <span class="required">*</span></b></td>
                    <td><input type="text" name="lname" size="30" value="" required
                               oninvalid="this.setCustomValidity('Please enter a last name')"
                               oninput="this.setCustomValidity('')"></td>
                </tr>
                <tr>
                    <td><b>Address <span class="required">*</span></b></td>
                    <td><input type="text" name="address" size="40" value="" required
                               oninvalid="this.setCustomValidity('Please enter an address')"
                               oninput="this.setCustomValidity('')"></td>
                </tr>
                <tr>
                    <td><b>Email <span class="required">*</span></b></td>
                    <td><input type="email" name="cmail" size="40" value="" required
                               oninvalid="this.setCustomValidity('Please enter an email')"
                               oninput="this.setCustomValidity('')"></td>
                </tr>
                <tr>
                    <td><b>Phone Number</b></td>
                    <td><input type="tel" name="pnumber" size="15" value=""></td>
                </tr>
                <tr>
                    <td><b>Mobile Number</b></td>
                    <td><input type="tel" name="mnumber" size="15" value=""></td>
                </tr>
                <tr>
                    <input type="hidden" value="N" name='maillist'>
                    <td><b>Mailing list</b></td>
                    <td><input type="checkbox" name="maillist" value="Y"> Subscribed to the mailing list</td><br>
                </tr>
                </br>
                <tr>
                    <td><b>Set Password <span class="required">*</span></b></td>
                    <td><input type="password" name="cpassword" size="25" value="" required
                               oninvalid="this.setCustomValidity('Please enter a password')"
                               oninput="this.setCustomValidity('')"></td>
                </tr>
                </br>
                <table align="center">
                    <tr>
                        <td><input type="submit" value="Add client"></td>
                        <td><input type="button" value="Return to List" OnClick="window.location='displayclient.php'"></td>
                    </tr>
                </table>
            </table>
        </form>
        <?php
        break;


        case "ConfirmInsert":
            $firstname = $_POST['fname'];
            $lastname = $_POST['lname'];
            $caddr = $_POST['address'];
            $email = $_POST['cmail'];
            $mobnum = $_POST['mnumber'];
            $phonenum = $_POST['pnumber'];
            $subscribe = $_POST['maillist'];
            $passw = $_POST['cpassword'];

            if(!isset($firstname) || trim($firstname) == '' || !isset($lastname) || trim($lastname) == '' ||
                !isset($caddr) || trim($caddr) == '' || !isset($email) || trim($email) == '' ||
                !isset($passw) || trim($passw) == '') {
                echo '<center></br></br><h3>Please fill out all of the required fields.  </h3></center></br>';
                echo '<center><p>Click <a href="AddClient.php?Action=Add">here</a> if not redirected</p></center>';
                header("refresh: 4, url=AddClient.php?Action=Add");
            }
            else {
                $hashed_pword = hash('sha256', $_POST['cpassword']);

                $insert_query = "INSERT INTO user(user_fname, user_lname, user_address, 
                user_email, user_mobile, user_phone, user_maillist, user_role, user_password) 
                VALUES(?,?,?,?,?,?,?,?,?)";
                $stmt = $dbh->prepare($insert_query);
                if (!$stmt->execute([$_POST["fname"], $_POST["lname"], $_POST["address"], $_POST["cmail"], $_POST["mnumber"],
                    $_POST["pnumber"], $_POST["maillist"], 'C', $hashed_pword])) {
                    $str = "<h2>Error adding client</h2><p>An error occurred while trying to add the client</p>";
                    commitOrRollback($dbh,'N',$str,"displayclient.php");
                } else{
                    $str = "<h2>Client added</h2><p>The client has been successfully added</p>";
                    commitOrRollback($dbh,'N',$str,"displayclient.php");
                }
                break;
            }
    }
    ?>


    </body>
    <?php
    $file = "displayclient.php";
    $add = "addClient.php";
    $modify = "UserModify.php";
    $view = "ViewClient.php";
    echo "<center><a href='DisplaySource.php?filename=".$file."&addname=".$add."&modifyname=".$modify."&viewname=".$view."' target='_blank'> <img src = 'phpPrint/client.png' class='img-thumbnail' width='200' height='40'> </a></center>";

    ?>
    </html>
