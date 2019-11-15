<?php
ob_start();
session_start();
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    header("Location: login.php?Action=Login");
}

require_once "connection.php";
?>

<html>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
<link href="css/styles.css" rel="stylesheet">

<head>
    <title>Project</title>
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
<h1 style="color: white;">Project</h1>

    <input type="button" value='Add a Project' OnClick='window.location="AddProject.php?Action=Add"'>
    <table class="table table-striped table-hover" style="background-color: white; margin-top: 10px">
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Funds</th>
        <th>Location</th>
        <th colspan="2">Options</th>
        <?php
        $query = "SELECT * FROM project";
        $stmt = $dbh->prepare($query);
        if (!$stmt->execute()) {
            ?>
            <title>Error occurred</title>
            <center>
                <h2>Error retrieving project details</h2>
                <p>An error occurred while trying to retrieve the project details</p>
            </center>
            <?php
        } else {
            while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                echo '<tr>
        <td>'. $row['project_id'].'</td>
        <td>'. $row['project_name']. '</td>
        <td>'. $row['project_desc'].'</td>
        <td>'. $row['project_raised'].'/'. $row['project_goal'].'</td>
        <td>'. $row['project_location'].'</td>
        <td>'. '<a href=ProjectModify.php?projno='. $row['project_id']. '&Action=Update>Update</a></td>
        <td>'. '<a href=ProjectModify.php?projno='. $row['project_id']. '&Action=Delete>Delete</a></td>
        </tr>';
            }
        }
        ?>
    </table>
</body>

<center>
    <?php
    $file = "displayproject.php";
    $add = "AddProject.php";
    $modify = "ProjectModify.php";
    $view = "empty.php";
    echo "<center><a href='DisplaySource.php?filename=".$file."&addname=".$add."&modifyname=".$modify."&viewname=".$view."' target='_blank'> <img src = 'phpPrint/project.png' class='img-thumbnail' width='200' height='40'> </a></center>";
    ?>

</center>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

</html>