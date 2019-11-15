<h1>Database Manipulation Test</h1>

<h3>
    <a href="insertClient.php">Insert a Client</a>
    <a href="modifyClient.php">Modify a Client</a>
    <a href="showDB.php">Show Database</a>
</h3>

<?php
ob_start();
?>
<html>
<head><title></title></head>
<link rel="stylesheet" type="text/css" href="style.css">
<body>
<script language="javascript">
    function confirm_delete()
    {
        window.location='custModify.php?clientID=<?php echo $_GET["clientID"]; ?>&Action=ConfirmDelete';
    }
</script>
<center><h3>Customer Modification</h3></center>
<?php
$conn = mysqli_connect("130.194.7.82", "s29726115", "monash00", "s29726115");
$query = "SELECT * FROM client";
$Host= "130.194.7.82";
$DB = "s29726115";
$UName = "s29726115";
$PWord = "monash00";
$dsn= "mysql:host=$Host;dbname=$DB";
$dbh = new PDO($dsn,$UName,$PWord);
$query="SELECT * FROM client WHERE clientID =".$_GET["clientID"];
$stmt = $dbh->prepare($query);
$stmt->execute();
$row=$stmt->fetchObject();

$strAction = $_GET["Action"];

switch($strAction)
{
case "Update":
    ?>
    <form method="post" action="custModify.php?clientID=<?php echo $_GET["clientID"]; ?>&Action=ConfirmUpdate">
        <center>Customer details amendment<br /></center><p />
        <table align="center" cellpadding="3">
            <tr />
            <td><b>Client ID</b></td>
            <td><?php echo $row->clientID; ?></td>
            </tr>
            <td><b>Client Name</b></td>
            <td><input type="text" name="cName" size="30" value="<?php echo $row->clientName; ?>"></td>
            </tr>
        </table>
        <br/>
        <table align="center">
            <tr>
                <td><input type="submit" value="Update Customer"></td>
                <td><input type="button" value="Return to List" OnClick="window.location='showDB.php'"></td>
            </tr>
        </table>
    </form>
    <?php
    break;

case "ConfirmUpdate":
    $query="UPDATE client set clientName='$_POST[cName]' WHERE clientID =".$_GET["clientID"];
    $stmt = $dbh->prepare($query);
    $stmt->execute();

    header("Location: showDB.php");

    break;

case "Delete":
    ?>
    <center>Confirm deletion of the following customer record<br /></center><p />
    <table align="center" cellpadding="3">
        <tr />
        <td><b>Client ID</b></td>
        <td><?php echo $row->clientID; ?></td>
        </tr>
        <tr>
            <td><b>Name</b></td>
            <td><?php echo "$row->clientName"; ?></td>
        </tr>
    </table>
    <br/>
    <table align="center">
        <tr>
            <td><input type="button" value="Confirm" OnClick="confirm_delete();">
            <td><input type="button" value="Cancel" OnClick="window.location='showDB.php'"></td>
        </tr>
    </table>
    <?php
    break;

case "ConfirmDelete":
$query="DELETE FROM client WHERE clientID =".$_GET["clientID"];
$stmt = $dbh->prepare($query);
if($stmt->execute())
{
?>
<center>
    The following customer record has been successfully deleted<p />
    <?php
    echo "Customer No. $row->clientID $row->clientName";
    echo "</center><p />";
    }
    else
    {
        echo "<center>Error deleting customer record<p /></center>";
    }
    echo "<center><input type='button' value='Return to List' OnClick='window.location=\"showDB.php\"'></center>";
    break;
    }
    $stmt->closeCursor();
    ?>
</body>
</html>