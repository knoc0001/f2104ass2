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
        <title>Delete Images</title>
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
    <body>

    <?php
    require_once 'connection.php';
    require_once 'CommitAndRollback.php';
    $query = "SELECT * FROM product JOIN prod_img pi ON product.product_id = pi.product_id ORDER BY pi.product_id";
    $stmt = $dbh->prepare($query);
    $files = glob("product_images/*.*");
    if (!$stmt->execute()) {
        ?>
        <title>Error occurred</title>
        <center>
            <h2>Error retrieving image details</h2>
            <p>An error occurred while trying to retrieve the image details</p>
        </center>
        <?php
        exit();
    }
    $strAction = $_GET["Action"];
    switch($strAction) {
        case "Delete":
        ?>
            <h2>Delete Multiple Images</h2>
            <form method="post" action="displayimages.php?Action=ConfirmDelete">
                <table class="table table-striped table-hover" style="background-color: white;">
                    <tr>
                        <th>Product Details</th>
                        <th>Image</th>
                        <th>Options</th>
                    </tr>
                    <?php
                    for ($i=0; $i<count($files); $i++) {
                        $image = basename($files[$i]);
                        $query = "SELECT * FROM product p JOIN prod_img pi ON p.product_id = pi.product_id";
                        $stmt = $dbh -> prepare($query);
                        $stmt -> execute();
                        $found = 0;
                        echo "<tr><td>";
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if ($row['img_name'] == $image) {
                            ?>
                                    <b><p><?php echo "Product no. ". $row["product_id"] . "- ". $row["product_name"]?></p></b>
                                    <p><?php echo $row['product_desc']?></p>
                            <?php
                                $found = 1;
                            }
                        }
                        if ($found === 0){
                            echo "<p>No product associated with this image</p>";
                        }
                        ?>
                        </td>
                        <td>
                            <img class="img-update" src="<?php echo "product_images/".$image;?>"/>
                        </td>
                        <td>
                            <input type="checkbox" name="images[]" value="<?php echo $image; ?>">
                        </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <center><input type="submit" value="Delete images"></center>
            </form>
        <?php
            break;

        case "ConfirmDelete":
            $return_link = "displayimages.php?Action=Delete";
            if (!empty($_POST["images"])) {
                $to_del_id = Array();
                foreach ($_POST['images'] as $curr_img) {
                    //gets array of image names to delete from prod_img table, if the image isn't in the table put -1
                    //in its associated index
                    $select_query = "SELECT * FROM prod_img WHERE img_name =?";
                    $stmt = $dbh->prepare($select_query);
                    $stmt->execute([$curr_img]);
                    if ($result = $stmt->fetch()) {
                        $to_push = $result['img_id'];
                    } else {
                        $to_push = -1;
                    }
                    array_push($to_del_id, $to_push);
                }

                //deletes image from product_images folder then relevant row from prod_img table
                for ($i=0; $i<count($_POST["images"]); $i++) {
                    if (!unlink("product_images/" . $_POST["images"][$i])) {
                        $str = "<h2>Error deleting product images</h2><p>An error occurred while attempting to delete the 
    checked images</p>";
                        commitOrRollback($dbh, "N", $str, $return_link);
                        die();
                    }
                    if ($to_del_id[$i] != -1) {
                        $query = "DELETE FROM prod_img WHERE img_id =?";
                        $stmt = $dbh->prepare($query);
                        if (!$stmt->execute([$to_del_id[$i]])) {
                            $str = "<h2>Error deleting product images</h2><p>An error occurred while attempting to delete the 
    checked images</p>";
                            commitOrRollback($dbh, "N", $str, $return_link);
                            die();
                        }
                    }
                }
                $str = "<h2>Deletion successful</h2><p>The images have been successfully deleted</p>";
                commitOrRollback($dbh, "N", $str, $return_link);
            }else {
                $str = "<h2>No images selected</h2><p>No images were selected for deletion</p>";
                commitOrRollback($dbh, "N", $str, $return_link);
                }
            break;
        }
    ?>


    </body>
    </html>
<?php
$file = "displayimages.php";
echo "<center><a href='DisplaySource2.php?filename=".$file."' target='_blank'> <img src = 'phpPrint/images.png' class='img-thumbnail' width='200' height='40'> </a></center>";
?>