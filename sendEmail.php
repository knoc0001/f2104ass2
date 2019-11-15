<html>
<head>
    <title>Send Email</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
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
ob_start();
session_start();
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    header("Location: login.php?Action=Login");
}

require_once 'connection.php';
require_once 'CommitAndRollback.php';
$checked_people = array();
$stmt = $dbh->prepare("SELECT user_fname, user_lname, user_email FROM user WHERE user_maillist=? ORDER BY 
user_fname");
if (!$stmt -> execute(['Y'])){
    $str = "<center><h2>Error retrieving client details</h2><p>An error occurred while trying to retrieve the client 
 details</p></center>";
    commitOrRollback($dbh,'N',$str,"displayclient.php");
    exit();
}
?>
<center>
    <h2>Send an Email</h2>
    <p>All required fields are marked with a <span class="required">*</span></p>
</center>

<?php
if (!isset($_POST["user_array"]))
{
    ?>
    <form method="post" action="sendEmail.php">
        <table width="100%">
            <tr>
                <td>To<span class="required">*</span></td>
                <td> |
                    <?php
                    while ($user_list = $stmt->fetchobject()) {
                        ?>
                        <input type="checkbox" name="user_array[]" value="<?php echo ($user_list->user_email); ?>">
                        <?php
                        echo $user_list->user_fname.' '.$user_list->user_lname. ' | ';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Subject<span class="required">*</span></td>
                <td><input type="text" name="subject" size="45" required
                           oninvalid="this.setCustomValidity('Please enter a subject')"
                           oninput="this.setCustomValidity('')"></td>
            </tr>
            <tr>
                <td>Message<span class="required">*</span></td>
                <td valign="top" align="left">
                    <textarea cols="70" name="message" rows="9" required
                              oninvalid="this.setCustomValidity('Please enter a message')"
                              oninput="this.setCustomValidity('')"></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2"><br /><br /><input type="submit" value="Send Email">
                    <input type="reset" value="Reset">
                </td>
            </tr>
        </table>
    </form>

    <form>
        <input type="button" value="Return to the previous page" onclick="history.back()">
    </form>

    <?php
}
else
{

    $from = "From: Famox Charities <knoc0001@student.monash.edu>";
    //$to = $_POST["to"];
    $msg = $_POST["message"];
    $subject = $_POST["subject"];
    $email_array = $_POST["user_array"];
    $to = implode(", ", $email_array);

    ini_set("SMTP", "smtp.monash.edu.au");
    ini_set("sendmail_from", "knoc0001@student.monash.edu");
    ini_set("smtp_port", "25");

    if(mail($to, $subject, $msg, $from))
    {
        echo "The mail has been successfully sent to the following email(s): ", $to;
        ?>
        </br>
        <td><input type="button" value="Return to List" OnClick="window.location='displayclient.php'"></td>
    <?php
    }
    else
    {
        echo "There was an error in sending this mail.";
        ?>
        <form>
            <input type="button" value="Return to the previous page" onclick="history.back()">
        </form>
        <?php
    }
}
?>



</body>
</html>

<?php
$file = "sendEmail.php";
echo "<center><a href='DisplaySource.php?filename=".$file."' target='_blank'> <img src = 'phpPrint/client.png' class='img-thumbnail' width='200' height='40'> </a></center>";
?>