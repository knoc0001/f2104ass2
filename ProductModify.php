<?php
ob_start();
session_start();
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    header("Location: login.php?Action=Login");
}

function matchString($cat_str, $cat_array)
{
    $strSelected = "";
    foreach ($cat_array as $curr_cat) {
        if ($cat_str === $curr_cat) {
            $strSelected = "checked";
        }
    }
    return $strSelected;
}

//connecting to database and querying product details
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
<?php
$query="SELECT * FROM product WHERE product_id =?";
$stmt = $dbh->prepare($query);
if (!$stmt->execute([$_GET["prodno"]])) {
    $str = "<h2>Error updating product</h2><p>The product details could not be retrieved</p>";
    commitOrRollback($dbh, 'N', $str,'MultipleProductModify.php');
    exit();
}
$row=$stmt->fetchObject();

$strAction = $_GET["Action"];
switch($strAction) {
case "Update":
    ?>
    <title>Update Product</title>
<body>
<center><h2>Update Product</h2>
    <p>All required fields are marked with a <span class="required">*</span></center>

    <form method="post" action="ProductModify.php?prodno=<?php echo $_GET["prodno"]; ?>&Action=ConfirmUpdate"
          enctype="multipart/form-data">
        <table align="center" cellpadding="3">
            <tr>
                <td><b>Name<span class="required">*</span></b></td>
                <td><input type="text" name="name" size="30" value="<?php echo $row->product_name; ?>" required
                           oninvalid="this.setCustomValidity('Please enter a product name')"
                           oninput="this.setCustomValidity('')"></td>
            </tr>
            <tr>
                <td><b>Description<span class="required">*</span></b></td>
                <td><textarea name="desc" cols="50" rows="5" required
                              oninvalid="this.setCustomValidity('Please enter a description')"
                              oninput="this.setCustomValidity('')"><?php echo $row->product_desc; ?></textarea></td>
            </tr>
            <tr>
                <td><b>Price<span class="required">*</span></b></td>
                <td><input type="number" name="price" size="20" value="<?php echo $row->product_price; ?>" required
                           oninvalid="this.setCustomValidity('Please enter a valid price (maximum of 2 decimal points)')"
                           oninput="this.setCustomValidity('')" min="0" step="0.01"></td>
            </tr>
            <tr>
                <td><b>Category</b></td>
                <td>
                    <table>
                    <?php
                    //gets IDs for all categories associated with current product, puts them in an array
                    $category_stmt = $dbh->prepare("SELECT category_id FROM product_category WHERE 
                        product_id = ?");
                    $category_stmt->execute([$row->product_id]);
                    $checked_category = array();
                    while ($current_category = $category_stmt->fetchobject()) {
                        $checked_category[] = $current_category->category_id;
                    }
                    //gets all category IDs and their associated names ordered alphabetically
                    $stmt = $dbh->prepare("SELECT category_id, category_name FROM category ORDER BY 
                        category_name");
                    $stmt->execute();
                    //compare values from array with all the categories, if an ID exists in the array its checkbox is checked
                    while ($category_list = $stmt->fetchobject()) {
                        echo "<tr><td>" . $category_list->category_name."</td>";
                        ?>
                        <td><input type="checkbox" name="category_array[]" value="<?php echo($category_list->category_id); ?>"
                            <?php echo matchString($category_list->category_id, $checked_category); ?>></td></tr>
                    <?php
                    }
                    ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td><b>Images</b></td>
                <td>
                    <table>
                        <?php
                        $stmt = $dbh->prepare("SELECT * FROM prod_img WHERE product_id = ?");
                        $stmt->execute([$row->product_id]);
                        while ($curr_img = $stmt->fetchobject()) {
                            $img_filename = $curr_img -> img_name;
                            ?>
                            <tr><td><img class="img-update" src="<?php echo "product_images/".$img_filename; ?>"</td>
                            <td>Delete <input type="checkbox" name="del_img[]" value="<?php echo($curr_img -> img_id); ?>"</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Select image to upload</b>
                <td><input type="file" name="file" accept="image/*"></td>
                </td>
            </tr>
        </table>
        <br>
        <table align="center">
            <tr>
                <td><input type="submit" value="Update Product"></td>
                <td><input type="button" value="Return to List" OnClick="window.location='displayproductrec.php'"></td>
            </tr>
        </table>
    </form>
    <?php
    break;

    case "ConfirmUpdate":
    $start_query = "START TRANSACTION;";
    $stmt = $dbh -> prepare($start_query);
    $stmt->execute();
    $return_link = "displayproductrec.php";

    //updates the product name, description and price
    $update_query = "UPDATE product SET product_name=?, product_desc=?, product_price=? WHERE product_id =?";
    $stmt = $dbh->prepare($update_query);
    if (!$stmt->execute([$_POST["name"],$_POST["desc"],$_POST["price"],$_GET["prodno"]])) {
        //update query failed, show error message
        $str = "<h2>Error updating product</h2><p>An error occurred while attempting to update the product details</p>";
        commitOrRollback($dbh,"R", $str,$return_link);
        die();
    }

    //deletes all product category entries for current product
    $delete_query = "DELETE FROM product_category WHERE product_id =?";
    $stmt = $dbh->prepare($delete_query);
    if (!$stmt->execute([$_GET["prodno"]])) {
        $str = "<h2>Error updating product categories</h2><p>An error occurred while attempting to update the product's 
categories</p>";
        commitOrRollback($dbh,"R", $str,$return_link);
        die();
    }
    //inserts all the checked categories into product_category table for the current product
    if (!empty($_POST['category_array'])) {
        foreach ($_POST["category_array"] as $selected) {
            $insert_query = "INSERT INTO product_category VALUES (?,?)";
            $stmt = $dbh->prepare($insert_query);
            if (!$stmt->execute([$_GET["prodno"],$selected])) {
                //insert query failed, rollback changes and show error message
                $str = "<h2>Error updating product categories</h2><p>An error occurred while attempting to update the 
product's categories</p>";
                commitOrRollback($dbh,"R", $str,$return_link);
                die();
            }
        }
    }

    if(is_uploaded_file($_FILES['file']['tmp_name'])) {
        //checks file name doesn't already exist, if it does add a number to the end increment by 1 until no file names match
        //inserts image details into prod_img table and uploads image into product_images folder
        $filename = $_FILES['file']['name'];
        $actual_name = pathinfo($filename,PATHINFO_FILENAME);
        $original_name = $actual_name;
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $i = 1;
        while(file_exists('product_images/'.$actual_name.".".$extension))
        {
            $actual_name = (string)$original_name.$i;
            $filename = $actual_name.".".$extension;
            $i++;
        }
        $target_file = "product_images/" . $filename;
        if (!(move_uploaded_file($_FILES["file"]["tmp_name"], $target_file))) {
            //error occurred while trying to upload image, rollback changes and show error message
            $str = "<h2>Error uploading product image</h2><p>An error occurred while attempting to upload the image 
'$filename'</p>";
            commitOrRollback($dbh,"R", $str,$return_link);
            die();
        }
        //inserts image details into prod_img
        $img_query = "INSERT INTO prod_img(img_name, product_id) VALUES(?,?);";
        $stmt = $dbh -> prepare($img_query);
        if (!$stmt -> execute([$filename,$_GET["prodno"]])){
            //error occurred while attempting to insert new image details into prod_img, rollback changes and show error message
            $str = "<h2>Error uploading product image</h2><p>An error occurred while attempting to upload the image 
'$filename'</p>";
            commitOrRollback($dbh,"R", $str,$return_link);
            die();
        }
    }

    //get names and IDs of images to be deleted
    if (!empty($_POST['del_img'])) {
        $to_del_name = Array();
        $to_del_id = Array();
        foreach ($_POST['del_img'] as $curr_img) {
            $select_query = "SELECT * FROM prod_img WHERE img_id =?";
            $stmt = $dbh->prepare($select_query);
            if (!$stmt->execute([$curr_img])) {
                //error occurred while trying to query database and/or get img_name, rollback changes and show error message
                $str = "<h2>Error deleting product images</h2><p>An error occurred while attempting to delete the 
checked images</p>";
                commitOrRollback($dbh, "R", $str, $return_link);
                die();
            }
            $result = $stmt->fetch();
            array_push($to_del_name, $result['img_name']);
            array_push($to_del_id, $result['img_id']);
        }

        //delete the images from product_images folder and relevant rows from the prod_img table
        $i = 0;
        $array_length = count($to_del_id);
        while ($i<$array_length) {
            if (!unlink("product_images/" . $to_del_name[$i])) {
                $str = "<h2>Error deleting product images</h2><p>An error occurred while attempting to delete the 
checked images</p>";
                commitOrRollback($dbh, "R", $str, $return_link);
                die();
            }
            $query = "DELETE FROM prod_img WHERE img_id =?";
            $stmt = $dbh->prepare($query);
            if (!$stmt->execute([$to_del_id[$i]])) {
                $str = "<h2>Error deleting product images</h2><p>An error occurred while attempting to delete the 
checked images</p>";
                commitOrRollback($dbh, "R", $str, $return_link);
                die();
            }
            $i++;
        }
    }
    //commits changes and shows message indicating product has been successfully updated
    $str = "<h2>Update successful</h2><p>The product has been successfully updated.</p>";
    commitOrRollback($dbh,"C", $str,$return_link);
    break;

case "Delete":
    ?>
<title>Delete Product</title>
<body>
<center><h1>Delete Product</h1></center>
    <center>Confirm deletion of the following product record<br/></center><p/>
    <table align="center" cellpadding="3">
        <tr/>
        <td><b>Product ID:</b></td>
        <td><?php echo $row->product_id; ?></td>
        </tr>
        <tr>
            <td><b>Product name:</b></td>
            <td><?php echo "$row->product_name"; ?></td>
        </tr>
    </table>
    <br/>
    <table align="center">
        <tr>
            <td><input type="button" value="Confirm" OnClick="window.location='ProductModify.php?prodno=<?php echo
                $_GET["prodno"]; ?>&Action=ConfirmDelete'">
            <td><input type="button" value="Cancel" OnClick="window.location='displayproductrec.php'"></td>
        </tr>
    </table>
    <?php
    break;

case "ConfirmDelete":
$query = "DELETE FROM product WHERE product_id =?";
$stmt = $dbh->prepare($query);
$return_link = "displayproductrec.php";
if ($stmt->execute([$_GET["prodno"]]))
{
?>
<head>
    <title>Successfully deleted</title>
</head>

<center>
<h1>Successfully deleted</h1>
    <p>The following product has been successfully deleted</p>
</center>
    <table align="center">
        <tr/>
        <td><b>Product ID:</b></td>
        <td><?php echo $row->product_id; ?></td>
        </tr>
        <tr>
            <td><b>Product name:</b></td>
            <td><?php echo "$row->product_name"; ?></td>
        </tr>
    </table>
</body>
<?php
echo "<center><input type='button' value='Return to List' onclick=\"window.location.href = '$return_link'\"></center>";
    }
else {
    $str = "<h2>Unsuccessful deletion</h2><p>Error deleting product ".$row->product_id."- ".$row->product_name.".</p>";
    commitOrRollback($dbh,'N',$str,$return_link);
}
break;
}
?>
</body>

<br>
<center><h5>Please note that the source code of 'product_category' is embedded within ProductModify's display code.  It does not have its own page.</h5></center>
<br>

</html>

<?php
$file = "displayproductrec.php";
$add = "InsertNewProduct.php";
$modify = "ProductModify.php";
$view = "ViewProduct.php";
echo "<center><a href='DisplaySource.php?filename=".$file."&addname=".$add."&modifyname=".$modify."&viewname=".$view."' target='_blank'> <img src = 'phpPrint/products.png' class='img-thumbnail' width='200' height='40'> </a></center>";


