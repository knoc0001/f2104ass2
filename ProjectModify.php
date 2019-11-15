<?php
ob_start();
session_start();
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    header("Location: login.php?Action=Login");
}
require_once 'connection.php';
require_once 'CommitAndRollback.php';
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
            <li><a class="nav-link text-white" href="documentation.php">Documentation</a></li>

            <li>
                <a class="nav-link text-white" href="emptypage.php"><img src="css/cart.png" class="img-fluid" alt="Responsive image"></a>
            </li>
        </ul>
    </div>
</nav>

</br></br></br></br>

<body>
<script language="javascript">
    function confirm_delete()
    {
        window.location='ProjectModify.php?projno=<?php echo $_GET["projno"]; ?>&Action=ConfirmDelete';
    }
</script>
<?php
$query="SELECT * FROM project WHERE project_id =?";
$stmt = $dbh->prepare($query);
if (!$stmt->execute([$_GET["projno"]])){
    $str =" <center><h2>Error retrieving project details</h2><p>An error occurred while trying to retrieve the project 
 details</p></center>";
    commitOrRollback($dbh,'N',$str,"displayproject.php");
    exit();
}
$row=$stmt->fetchObject();

$strAction = $_GET["Action"];

switch($strAction)
{
case "Update":
    ?>
    <title>Update Project</title>
    <form method="post" action="ProjectModify.php?projno=<?php echo $_GET["projno"]; ?>&Action=ConfirmUpdate">
        <center><h2>Update Project</h2>
            <p>All required fields are marked with a <span class="required">*</span></p></center>
        <table align="center">
            <tr />
            <td><b>Project No.</b></td>
            <td><?php echo $row->project_id; ?></td>
            </tr>
            <tr>
                <td><b>Project Name<span class="required">*</span></b></td>
                <td><input type="text" name="pname" size="30" value="<?php echo $row->project_name; ?>" required
                           oninvalid="this.setCustomValidity('Please enter a project name')"
                           oninput="this.setCustomValidity('')"></td>
            </tr>
            <tr>
                <td><b>Project Description<span class="required">*</span></b></td>
                <td><textarea name="pdescription" cols="50" rows="5" required
                              oninvalid="this.setCustomValidity('Please enter a description')"
                              oninput="this.setCustomValidity('')"><?php echo $row->project_desc; ?></textarea></td>
            </tr>
            <tr>
                <td><b>Raised Amount<span class="required">*</span></b></td>
                <td><input type="number" name="praised" size="10" value="<?php echo $row->project_raised; ?>" required
                           oninvalid="this.setCustomValidity('Please enter the amount raised')"
                           oninput="this.setCustomValidity('')"></td>
            </tr>
            <tr>
                <td><b>Project Goal<span class="required">*</span></b></td>
                <td><input type="number" name="pgoal" size="10" value="<?php echo $row->project_goal; ?>" required
                           oninvalid="this.setCustomValidity('Please enter a the project goal')"
                           oninput="this.setCustomValidity('')"></td>
            </tr>
            <tr>
                <td><b>Project Location<span class="required">*</span></b></td>
                <td><input type="text" name="plocation" size="20" value="<?php echo $row ->project_location; ?>" required
                           oninvalid="this.setCustomValidity('Please enter the project location')"
                           oninput="this.setCustomValidity('')"></td><br>
            </tr>

        </table>
        <br/>
        <table align="center">
            <tr>
                <td><input type="submit" value="Update Project"></td>
                <td><input type="button" value="Return to List" OnClick="window.location='displayproject.php'"></td>
            </tr>
        </table>
    </form>
    <?php
    break;

case "ConfirmUpdate":
    $query="UPDATE project SET project_name=?, project_desc=?, project_goal=?, project_raised=?, project_location=? 
WHERE project_id =?";
    $stmt = $dbh->prepare($query);
    if (!$stmt->execute([$_POST["pname"],$_POST["pdescription"],$_POST["pgoal"],$_POST["praised"],$_POST["plocation"],
        $_GET["projno"]])){
        ?>
        <title>Error updating project</title>
        <center><h2>Error updating project</h2>
        <br>
        <input type='button' value='Return to List' OnClick='window.location="displayproject.php"'></center>
<?php
    }
    ?>
    <body>
    <center><h2>The project has been updated</h2>
        <br>
        <input type='button' value='Return to List' OnClick='window.location="displayproject.php"'></center>
    </body>
    <?php
    break;

case "Delete":
    ?>
    <title>Delete Project</title>
    <center>
        <h2>Confirm deletion</h2>
        <p>Confirm deletion of the following project record</p>
    </center>
    <table align="center">
        <tr />
        <td><b>Project. No.</b></td>
        <td><?php echo $row->project_id; ?></td>
        </tr>
        <tr>
            <td><b>Name</b></td>
            <td><?php echo "$row->project_name"; ?></td>
        </tr>
    </table>
    <br/>
    <table align="center">
        <tr>
            <td><input type="button" value="Confirm" OnClick="confirm_delete();">
            <td><input type="button" value="Cancel" OnClick="window.location='displayproject.php'"></td>
        </tr>
    </table>
    <?php
    break;

case "ConfirmDelete":
$query="DELETE FROM project WHERE project_id =?";
$stmt = $dbh->prepare($query);
if($stmt->execute([$_GET["projno"]]))
{
?>
<center>
    <h2>Deletion successful</h2><p>
    <?php
    echo "Project no. $row->project_id $row->project_name ";
    echo "has been successfully deleted</p></center>";
    }
    else {
        echo "<center><h2>Deletion unsuccessful</h2><p>Project no. $row->project_id $row->project_name was not 
deleted</p></center>";
    }
    echo "<center><input type='button' value='Return to List' OnClick='window.location=\"displayproject.php\"'></center>";
    break;
    }
    $stmt->closeCursor();
    ?>
</body>
</html>

<?php
$file = "displayproject.php";
$add = "AddProject.php";
$modify = "ProjectModify.php";
$view = "empty.php";
echo "<center><a href='DisplaySource.php?filename=".$file."&addname=".$add."&modifyname=".$modify."&viewname=".$view."' target='_blank'> <img src = 'phpPrint/project.png' class='img-thumbnail' width='200' height='40'> </a></center>";
?>
