<?php
ob_start();
session_start();
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    header("Location: login.php?Action=Login");
}
?>
<html>
<head>
    <title>Add Category</title>
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
<?php
require_once 'connection.php';
require_once 'CommitAndRollback.php';
$query="SELECT * FROM category";
$stmt = $dbh->prepare($query);
if (!$stmt->execute()){
    $str ="<h2>Error retrieving category details</h2><p>An error occurred while trying to retrieve the category 
 details</p>";
    commitOrRollback($dbh,'N',$str,"displaycategory.php");
    exit();
}

$row=$stmt->fetchObject();
$strAction = $_GET["Action"];
switch($strAction)
{
    case "Add":
        ?>
        <form method="post" action="AddCategory.php?Action=ConfirmInsert">
            <center><h2>Add a Category</h2>
            <p>All required fields are marked with a <span class="required">*</span></p></center>
            <table align="center">
                <tr>
                    <td><b>Category Name <span class="required">*</span></b></td>
                    <td><input type="text" name="name" size="30" value="" required
                               oninvalid="this.setCustomValidity('Please enter a name')"
                               oninput="this.setCustomValidity('')"></td>
                </tr>
            <br>
            <table align="center">
                <tr>
                    <td><input type="submit" value="Add category"></td>
                    <td><input type="button" value="Return to List" OnClick="window.location='displaycategory.php'"></td>
                </tr>
            </table>
        </form>
        <?php
        break;


    case "ConfirmInsert":
        $insert_query = "INSERT INTO category(category_name) VALUES(?)";
        $stmt = $dbh->prepare($insert_query);
        if ($stmt->execute([$_POST["name"]])){
            $str ="<h2>Category successfully added</h2><p>The category has been successfully added</p>";
            commitOrRollback($dbh,'N',$str,"displaycategory.php");
        } else{
            $str ="<h2>Error adding category</h2><p>An error occurred while trying to add the category</p>";
            commitOrRollback($dbh,'N',$str,"displaycategory.php");
        }
        break;
}

$stmt->closeCursor();
?>

</body>
</html>

<?php
$file = "displaycategory.php";
$add = "AddCategory.php";
$modify = "CategoryModify.php";
$view = "empty.php";
echo "<center><a href='DisplaySource.php?filename=".$file."&addname=".$add."&modifyname=".$modify."&viewname=".$view."' target='_blank'> <img src = 'phpPrint/category.png' class='img-thumbnail' width='200' height='40'> </a></center>";
?>
