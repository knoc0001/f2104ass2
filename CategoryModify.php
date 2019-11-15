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
//connecting to database and querying category details
require_once 'connection.php';
require_once 'CommitAndRollback.php';
$query="SELECT * FROM category WHERE category_id =?";
$stmt = $dbh->prepare($query);
if (!$stmt->execute([$_GET["catno"]])) {
    $str ="<h2>Error retrieving category details</h2><p>An error occurred while trying to retrieve the category 
 details</p>";
    commitOrRollback($dbh,'N',$str,"displaycategory.php");
    exit();
    }
$row=$stmt->fetchObject();
$strAction = $_GET["Action"];

    switch($strAction) {
    case "Update":
    ?>
    <title>Update Category</title>
    <center><h2>Update Category</h2>
        <p>All required fields are marked with a <span class="required">*</span></p></center>
    <form method="post" action="CategoryModify.php?catno=<?php echo $_GET["catno"]; ?>&Action=ConfirmUpdate">
    <table align="center">
        <tr>
        <td><b>Category ID: </b></td>
        <td><?php echo $row->category_id; ?></td>
        </tr>
        <tr>
            <td><b>Category name<span class="required">*</span></b></td>
            <td><input type="text" name="name" size="30" value="<?php echo $row->category_name; ?>" required
                       oninvalid="this.setCustomValidity('Please enter a category name')"
                       oninput="this.setCustomValidity('')"></td>
        </tr>
    </table>
    <br>
    <table align="center">
        <tr>
            <td><input type="submit" value="Update Category"></td>
            <td><input type="button" value="Return to List" OnClick="window.location='displaycategory.php'"></td>
        </tr>
    </table>
    </form>
</body>
    <?php
    break;
    //updates category name
    case "ConfirmUpdate":
        $update_query = "UPDATE category SET category_name=? WHERE category_id =?";
        $stmt = $dbh->prepare($update_query);
        if ($stmt->execute([$_POST["name"], $_GET["catno"]])){
            $str ="<h2>Successfully updated</h2><p>The category has been updated</p>";
            commitOrRollback($dbh,'N',$str,"displaycategory.php");
        } else{
            $str ="<h2>Error updating category</h2><p>An error occurred while trying to update the category details</p>";
            commitOrRollback($dbh,'N',$str,"displaycategory.php");
            exit();
        }
        break;

    case "Delete":
    ?>
    <title>Delete Category</title>
    <center><h2>Delete Category</h2></center>
    <center>Confirm deletion of the following category<br/></center>
    <table align="center">
        <tr/>
        <td><b>Category ID:</b></td>
        <td><?php echo $row->category_id; ?></td>
        </tr>
        <tr>
            <td><b>Category name:</b></td>
            <td><?php echo "$row->category_name"; ?></td>
        </tr>
    </table>
    <br/>
    <table align="center">
        <tr>
            <td><input type="button" value="Confirm" OnClick="window.location='CategoryModify.php?catno=<?php echo
                $_GET["catno"]; ?>&Action=ConfirmDelete'">
            <td><input type="button" value="Cancel" OnClick="window.location='displaycategory.php'"></td>
        </tr>
    </table>
    <?php
    break;
    //deletes relevant entries from category and product_category table
    case "ConfirmDelete":
        $query = "DELETE FROM category WHERE category_id =?";
        $stmt = $dbh->prepare($query);
        if ($stmt->execute([$_GET["catno"]]))
        {
            ?>
                <title>Successfully deleted</title>
            <center>
                <h2>Successfully deleted</h2>
                <p>The following category has been successfully deleted</p>
            </center>
            <table align="center">
                <tr/>
                <td><b>Category ID:</b></td>
                <td><?php echo $row->category_id; ?></td>
                </tr>
                <tr>
                    <td><b>Category name:</b></td>
                    <td><?php echo "$row->category_name"; ?></td>
                </tr>
            </table>
            </body>
            <?php
        }
        else {
            ?>
            <title>Error deleting</title>
            <center>
                <h2>Error deleting category</h2>
                <p>An error occurred while trying to delete the following category</p>
            </center>
            <table align="center">
                <tr/>
                <td><b>Category ID:</b></td>
                <td><?php echo $row->category_id; ?></td>
                </tr>
                <tr>
                    <td><b>Category name:</b></td>
                    <td><?php echo "$row->category_name"; ?></td>
                </tr>
            </table>
            </body>
            <?php
        }
        echo "<center><input type='button' value='Return to List' OnClick='window.location=\"displaycategory.php\"'></center>";
        break;
    }
    ?>
</html>

<?php
$file = "displaycategory.php";
$add = "AddCategory.php";
$modify = "CategoryModify.php";
$view = "empty.php";
echo "<center><a href='DisplaySource.php?filename=".$file."&addname=".$add."&modifyname=".$modify."&viewname=".$view."' target='_blank'> <img src = 'phpPrint/category.png' class='img-thumbnail' width='200' height='40'> </a></center>";
?>
