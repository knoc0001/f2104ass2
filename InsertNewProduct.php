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
        <title>Add a Product</title>
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
    <br><br><br><br>
    <?php
    $strAction = $_GET["Action"];

    switch($strAction)
    {
    case "Add":
    ?>
    <body>
    <form method="post" enctype="multipart/form-data" action="InsertNewProduct.php?Action=ConfirmInsert">
        <center><h3>Add a Product</h3>
        <p>All required fields are marked with a <span class="required">*</span></p></center>
        <table align="center" cellpadding="3">
            <tr>
                <td><b>Product Name<span class="required">*</span></b></td>
                <td><input type="text" name="name" size="30" value="" required
                           oninvalid="this.setCustomValidity('Please enter a product name')"
                           oninput="this.setCustomValidity('')"></td>
            </tr>
            <tr>
                <td><b>Product Description<span class="required">*</span></b></td>
                <td><textarea name="desc" cols="50" rows="5" required
                              oninvalid="this.setCustomValidity('Please enter a description')"
                              oninput="this.setCustomValidity('')"></textarea></td>
            </tr>
            <tr>
                <td><b>Product Price<span class="required">*</span></b></td>
                <td><input type="number" name="price" size="20" value="" min="0" step="0.01" required
                           oninvalid="this.setCustomValidity('Please enter a valid price (maximum of 2 decimal points)')"
                           oninput="this.setCustomValidity('')"></td>
            </tr>
            <tr>
                <td><b>Category</b></td>
                <td>
                    <table>
                        <?php
                        $stmt = $dbh->prepare("SELECT category_id, category_name FROM category ORDER BY 
                        category_name");
                        $stmt -> execute();
                        while ($category_list = $stmt->fetchobject()) {
                            echo "<tr><td>".$category_list->category_name."</td>";
                            ?>
                            <td><input type="checkbox" name="category_array[]" value="<?php echo ($category_list->category_id); ?>">
                            </td></tr>
                            <?php
                        }
                        ?>
                    </table>
                </td>
            </tr>

            <tr>
                <td><b>Select file to upload:</b></td>
                <td><input type="file" name="file" accept="image/*"></td>
            </tr>
            <input type="hidden" name="date" size="30" value="<?php echo date('Y-m-d H:i:s') ?>">
        </table>

        <br/>
        <table align="center">
            <tr>
                <td><input type="submit" value="Insert Product"></td>
                <td><input type="button" value="Return to List" OnClick="window.location='displayproductrec.php'"></td>
            </tr>
        </table>
    </form>
    </body>
        <?php
            break;

        case "ConfirmInsert":
            $start_query = "START TRANSACTION;";
            $stmt = $dbh -> prepare($start_query);
            $stmt->execute();
            $return_link = "displayproductrec.php";

            //inserts product details into product table and its associated categories into product_category table
            $insert_query = "INSERT INTO product(product_name, product_desc, date_added, product_price) VALUES 
            (?,?,?,?);";
            $stmt = $dbh -> prepare($insert_query);
            if (!($stmt->execute([$_POST["name"], $_POST["desc"], $_POST["date"], $_POST["price"]]))) {
                //insert query failed, show error message
                $str = "<h2>Error inserting product</h2><p>An error occurred while attempting to add the new product</p>";
                commitOrRollback($dbh,"R", $str,$return_link);
                die();
            }
            $last_id = $dbh->lastInsertId();

            //insert into product_category table all the categories that have been checked
            if (!empty($_POST["category_array"])) {
                foreach ($_POST["category_array"] as $selected) {
                    $insert_query = "INSERT INTO product_category VALUES (?,?);";
                    $stmt = $dbh -> prepare($insert_query);
                    if (!($stmt->execute([$last_id, $selected]))) {
                        //insert query failed, rollback any changes made and show error message
                        $str = "<h2>Error inserting product</h2><p>An error occurred while attempting to add the new product's categories</p>";
                        commitOrRollback($dbh,"R", $str,$return_link);
                        die();
                    }
                }
            }

            //uploads the image into product_images folder
            if(is_uploaded_file($_FILES['file']['tmp_name'])) {
                //checks file name doesn't already exist, if it does add a number to the end increment by 1 until no file names match
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
                    $str = "<h2>Error uploading image</h2><p>An error occurred while attempting to upload '$filename'</p>";
                    commitOrRollback($dbh,"R", $str,$return_link);
                    die();
                }

                //inserts image details into prod_img
                $img_query = "INSERT INTO prod_img(img_name, product_id) VALUES(?,?);";
                $stmt = $dbh->prepare($img_query);
                if (!$stmt->execute([$filename, $last_id])) {
                    //error occurred while attempting to upload image details into prod_img, rollback changes and show error message
                    $str = "<h2>Error uploading image</h2><p>An error occurred while attempting to upload '$filename'</p>";
                    commitOrRollback($dbh,"R", $str,$return_link);
                    die();
                }
            }

            //commit changes and show message indicating the product has been successfully added
        ?>
    <?php
            $str = "<h2>Successfully added</h2><p>The product has been successfully added</p>";
            commitOrRollback($dbh,"C", $str,$return_link);
            break;
    }

    $stmt->closeCursor()
    ?>

    <?php
    $file = "displayproductrec.php";
    $add = "InsertNewProduct.php";
    $modify = "ProductModify.php";
    $view = "ViewProduct.php";
    echo "<center><a href='DisplaySource.php?filename=".$file."&addname=".$add."&modifyname=".$modify."&viewname=".$view."' target='_blank'> <img src = 'phpPrint/products.png' class='img-thumbnail' width='200' height='40'> </a></center>";
    ?>
</body>
</html>

