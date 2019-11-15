<?php
ob_start();
session_start();
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    header("Location: login.php?Action=Login");
}

function mailSubscribed($str)
{
    $strSelected = "";
    if ($str == "Y") {
        $strSelected = " checked";
    }
    return $strSelected;
}
?>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
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
        </ul>
    </div>
</nav>

<body>
</br></br></br></br>
<script language="javascript">
    function confirm_delete()
    {
        window.location='UserModify.php?clientno=<?php echo $_GET["clientno"]; ?>&Action=ConfirmDelete';
    }
</script>
<?php
require_once 'connection.php';
require_once 'CommitAndRollback.php';
$query="SELECT * FROM user WHERE user_id =?";
$stmt = $dbh->prepare($query);
if (!$stmt->execute([$_GET["clientno"]])){
    $str =" <center><h2>Error retrieving client details</h2><p>An error occurred while trying to retrieve the client 
 details</p></center>";
    commitOrRollback($dbh,'N',$str,"displayclient.php");
    exit();
}
$row=$stmt->fetchObject();

$strAction = $_GET["Action"];

switch($strAction)
{
case "Update":
    ?>
    <title>Update Client</title>
    <center>
        <h2>Update Client</h2>
        <p>All required fields are marked with a <span class="required">*</span></p>
    </center>
    <form method="post" action="UserModify.php?clientno=<?php echo $_GET["clientno"]; ?>&Action=ConfirmUpdate">
        <table align="center">
            <tr>
            <td><b>Client no.</b></td>
            <td><?php echo $row->user_id; ?></td>
            </tr>
            <tr>
                <td><b>First name<span class="required">*</span></td>
                <td><input type="text" name="fname" size="30" value="<?php echo $row->user_fname; ?>" required
                           oninvalid="this.setCustomValidity('Please enter a first name')"
                           oninput="this.setCustomValidity('')"></td>
            </tr>
            <tr>
                <td><b>Last name<span class="required">*</span></b></td>
                <td><input type="text" name="lname" size="30" value="<?php echo $row->user_lname; ?>" required
                           oninvalid="this.setCustomValidity('Please enter a last name')"
                           oninput="this.setCustomValidity('')"></td>
            </tr>
            <tr>
                <td><b>Address<span class="required">*</span></b></td>
                <td><input type="text" name="address" size="40" value="<?php echo $row->user_address; ?>" required
                           oninvalid="this.setCustomValidity('Please enter an address')"
                           oninput="this.setCustomValidity('')"></td>
            </tr>
            <tr>
                <td><b>Email<span class="required">*</span></b></td>
                <td><input type="email" name="email" size="40" value="<?php echo $row->user_email; ?>" required
                           oninvalid="this.setCustomValidity('Please enter an email')"
                           oninput="this.setCustomValidity('')"></td>
            </tr>
            <tr>
                <td><b>Phone number</b></td>
                <td><input type="tel" name="pnumber" size="10" value="<?php echo $row->user_phone; ?>"></td>
            </tr>
            <tr>
                <td><b>Mobile number</b></td>
                <td><input type="tel" name="mnumber" size="10" value="<?php echo $row->user_mobile; ?>"></td>
            </tr>
            <tr>
                <td><b>Mailing list</b></td>
                <input type="hidden" value="N" name='maillist'>
                <td><input type="checkbox" name="maillist" value="Y" <?php echo mailSubscribed($row ->
                    user_maillist); ?>> I want to subscribe to the mailing list</td><br>
            </tr>
            <tr>
                <td><b>Set new password</b></td>
                <td><input type="password" name="cpassword" size="25" value=""></td>
            </tr>
            </br>

        </table>
        <br/>
        <table align="center">
            <tr>
                <td><input type="submit" value="Update Customer"></td>
                <td><input type="button" value="Return to List" OnClick="window.location='displayclient.php'"></td>
            </tr>
        </table>
    </form>
    <?php
    break;

case "ConfirmUpdate":
    $firstname = $_POST['fname'];
    $lastname = $_POST['lname'];
    $caddr = $_POST['address'];
    $email = $_POST['email'];
    $mobnum = $_POST['mnumber'];
    $phonenum = $_POST['pnumber'];
    $subscribe = $_POST['maillist'];
    $passw = $_POST['cpassword'];

    if(!isset($firstname) || trim($firstname) == '' || !isset($lastname) || trim($lastname) == '' ||
        !isset($caddr) || trim($caddr) == '' || !isset($email) || trim($email) == '') {
        echo '<center></br></br><h2>Please fill out all of the required fields.  </h2></center></br>';
        echo '<center><p>Click <a href="displayclient.php">here</a> if not redirected</p></center>';
        header("refresh: 5, url=displayclient.php");
    }
    else {
        $hashed_pword = hash('sha256', $_POST['cpassword']);
        $query = "UPDATE user SET user_fname=?, user_lname=?, user_address=?, user_mobile=?, user_phone=?, user_email=?, 
                user_maillist=?, user_password=? WHERE user_id =?";
        $stmt = $dbh->prepare($query);
        if (!$stmt->execute([$_POST["fname"],$_POST["lname"],$_POST["address"],$_POST["mnumber"],$_POST["pnumber"],
            $_POST["email"], $_POST["maillist"],$hashed_pword,$_GET["clientno"]])){
            $str =" <center><h2>Error updating client </h2><p>An error occurred while trying to update the client 
 details</p></center>";
            commitOrRollback($dbh,'N',$str,"displayclient.php");
            exit();
        }
        ?>
        <center>
            <h2>The client has been successfully updated</h2>
            <input type='button' value='Return to List' onclick="window.location.href='displayclient.php'">
        </center>
<?php
    }
    break;




case "Delete":
    ?>
    <center>
        <h2>Delete Client</h2>
        <p>Confirm deletion of the following client record</p>
    </center>
    <table align="center">
        <tr />
        <td><b>Client no.</b></td>
        <td><?php echo $row->user_id; ?></td>
        </tr>
        <tr>
            <td><b>Name</b></td>
            <td><?php echo "$row->user_fname $row->user_lname"; ?></td>
        </tr>
    </table>
    <br/>
    <table align="center">
        <tr>
            <td><input type="button" value="Confirm" OnClick="confirm_delete();">
            <td><input type="button" value="Cancel" OnClick="window.location='displayclient.php'"></td>
        </tr>
    </table>
    <?php
    break;

case "ConfirmDelete":
$query="DELETE FROM user WHERE user_id =?";
$stmt = $dbh->prepare($query);
if($stmt->execute([$_GET["clientno"]]))
{
?>
<center>
    <h2>Successfully deleted</h2>
    <?php
    echo "<p>Client no.$row->user_id $row->user_fname $row->user_lname has been successfully deleted";
    echo "</p><input type='button' value='Return to List' OnClick='window.location=\"displayclient.php\"'></center>";
    }
    else
    {
        $str =" <h2>Error deleting client </h2><p>An error occurred while trying to delete the client </p>";
        commitOrRollback($dbh,'N',$str,"displayclient.php");
        exit();
    }
    break;

    }
    $stmt->closeCursor();
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