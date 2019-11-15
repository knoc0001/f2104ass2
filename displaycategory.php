<?php
ob_start();
session_start();
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    header("Location: login.php?Action=Login");
}
require_once "connection.php";
require_once "CommitAndRollback.php";
?>

<html>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
<link href="css/styles.css" rel="stylesheet">

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
    <title>Category</title>
</head>
<body>
<h1 style="color: white;">Category</h1>
<input type="button" value='Add a Category' OnClick='window.location="AddCategory.php?Action=Add"'>
<!--<a href=AddCategory.php?Action=Add style="color: white;"><u>Add a category</u></a></td><td>!-->
    <?php
    $query = "SELECT * FROM category";
    $stmt = $dbh->prepare($query);
    if (!$stmt->execute()){
        ?>
        <title>Error occurred</title>
        <center>
            <h2>Error retrieving client details</h2>
            <p>An error occurred while trying to retrieve the client details</p>
        </center>
        <?php
    }
    ?>
<table class="table table-striped table-hover" style="background-color: white; margin-top: 10px;">
    <th>ID</th>
    <th>Name</th>
    <th colspan="2">Options</th>
    <?php
    while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        echo '<tr><td>'.
            $row['category_id'].'</td><td>'.
            $row['category_name']. '</td><td>'.
            '<a href=CategoryModify.php?catno='. $row['category_id']. '&Action=Update>Update</a></td><td>'.
            '<a href=CategoryModify.php?catno='. $row['category_id']. '&Action=Delete>Delete</a></td></tr>';
    }
    ?>
</table>
</body>

<?php
$file = "displaycategory.php";
$add = "AddCategory.php";
$modify = "CategoryModify.php";
$view = "empty.php";
echo "<center><a href='DisplaySource.php?filename=".$file."&addname=".$add."&modifyname=".$modify."&viewname=".$view."' target='_blank'> <img src = 'phpPrint/category.png' class='img-thumbnail' width='200' height='40'> </a></center>";
?>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

</html>