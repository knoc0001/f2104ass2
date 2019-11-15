<?php
ob_start();
session_start();
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    header("Location: login.php?Action=Login");
}
?>

<html>
<head>
    <title>Add a Project</title>
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

<?php
require_once 'connection.php';
require_once 'CommitAndRollback.php';
$query="SELECT * FROM project";
$stmt = $dbh->prepare($query);
if (!$stmt->execute()){
    $str ="<h2>Error retrieving project details</h2><p>An error occurred while trying to retrieve the project 
 details</p>";
    commitOrRollback($dbh,'N',$str,"displayproject.php");
    exit();
}
$row=$stmt->fetchObject();
$strAction = $_GET["Action"];

switch($strAction)
{
    case "Add":
        ?>
    <body>
        <form method="post" enctype="multipart/form-data" action="AddProject.php?Action=ConfirmInsert">
            <center><h3>Add a project</h3>
                <p>All required fields are marked with a <span class="required">*</span></p><br /></center>
            <table align="center">
                <tr>
                    <td><b>Project Name <span class="required">*</span></b></td>
                    <td><input type="text" name="name" size="30" value="" required
                               oninvalid="this.setCustomValidity('Please enter a project name')"
                               oninput="this.setCustomValidity('')"></td>
                </tr>
                <tr>
                    <td><b>Project Description <span class="required">*</span></b></td>
                    <td><textarea name="desc" cols="50" rows="5" required
                                  oninvalid="this.setCustomValidity('Please enter a description')"
                                  oninput="this.setCustomValidity('')"></textarea></td>
                </tr>
                <tr>
                    <td><b>Current Donations <span class="required">*</span></b></td>
                    <td><input type="number" name="raised" size="10" min="0" step="0.01" value=""
                               required oninvalid="this.setCustomValidity('Please enter the current amount of donations (maximum of 2 decimal points)')"
                               oninput="this.setCustomValidity('')"> </td>
                </tr>
                <tr>
                    <td><b>Project Goal <span class="required">*</span></b></td>
                    <td><input type="number" name="goal" size="10" min="0" step="0.01" value=""
                               required oninvalid="this.setCustomValidity('Please enter the project goal (maximum of 2 decimal points)')"
                               oninput="this.setCustomValidity('')"></td>
                </tr>
                <tr>
                    <td><b>Project Location <span class="required">*</span></b></td>
                    <td><input type="text" name="location" size="30" value="" required
                               oninvalid="this.setCustomValidity('Please enter the project location')"
                               oninput="this.setCustomValidity('')"></td>
                </tr>
            </table>

            <br/>
            <table align="center">
                <tr>
                    <td><input type="submit" value="Insert Project"></td>
                    <td><input type="button" value="Return to List" OnClick="window.location='displayproject.php'"></td>
                </tr>
            </table>
        </form>
    </body>
        <?php
        break;


    case "ConfirmInsert":
        $insert1_query = "INSERT INTO project(project_name, project_desc, project_raised, project_goal, 
project_location) VALUES (?,?,?,?,?)";
        $stmt = $dbh->prepare($insert1_query);
        if ($stmt->execute([$_POST["name"], $_POST["desc"], $_POST["raised"], $_POST["goal"], $_POST["location"]])){
            ?>
            <body>
            <center><h2>The project has been added</h2>
                <br>
                <input type='button' value='Return to List' OnClick='window.location="displayproject.php"'></center>
            </body>
            <?php
        } else {
            ?>
            <body>
            <center><h2>Error adding the project</h2>
                <br>
                <input type='button' value='Return to List' OnClick='window.location="displayproject.php"'></center>
            </body>
            <?php
        }
        ?>
<?php
        break;
}

$stmt->closeCursor()
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
