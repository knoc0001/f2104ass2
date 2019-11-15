<?php
session_start();

if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    header("Location: login.php?Action=Login");
}

require_once 'connection.php';
require_once 'CommitAndRollback.php';
?>
<html>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="css/styles.css">

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<head>
    <meta name="viewport" content="width-device-width, initial-scale=1">
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
$curr_prod = $_GET['prodno'];
$query = "SELECT * FROM product WHERE product_id=?";
$stmt = $dbh -> prepare($query);
if (!$stmt -> execute([$curr_prod])){
    ?>
    <?php
    $str ="<h2>Error retrieving product details</h2><p>An error occurred while trying to retrieve the product 
 details</p>";
    commitOrRollback($dbh,'N',$str,"displayproductrec.php");
    exit();
}
$prod = $stmt -> fetchObject();
?>
<head>
    <title><?php echo $prod -> product_name;?></title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-4">
            <div id="productImages" class="carousel slide" data-interval="false">
                <div class="carousel-inner">
                    <?php
                    $stmt = $dbh->prepare("SELECT img_name FROM prod_img WHERE product_id =?");
                    $stmt->execute([$prod->product_id]);
                    $has_run = 0;
                    while ($curr_img = $stmt->fetchobject()) {
                        $img_filename = $curr_img -> img_name; ?>
                        <div class="carousel-item<?php if ($has_run === 0){echo " active";}?>">
                            <img class="d-block w-100" src="<?php echo "product_images/".$img_filename; ?>">
                        </div>
                        <?php
                        $has_run = 1;
                    }
                    ?>
                </div>
                <a class="carousel-control-prev" href="#productImages" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#productImages" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
        <div class="jumbotron col">
            <h1><?php echo $prod -> product_name;?></h1>
            <h2><?php echo '$'.$prod -> product_price;?></h2>
            <br>
            <p><?php echo $prod -> product_desc;?></p>
            <p><b>Product ID: </b><?php echo $prod -> product_id;?></p>
            <p><b>Category: </b><?php
                $arr = Array();
                $cat_query = "SELECT category_name FROM product_category pc JOIN category c ON c.category_id = 
                    pc.category_id WHERE product_id=?";
                $cat_stmt = $dbh -> prepare($cat_query);
                $cat_stmt -> execute([$curr_prod]);
                while ($cat = $cat_stmt -> fetchObject()) {
                    array_push($arr, ($cat -> category_name));
                }
                $str = implode (", ", $arr);
                echo $str;
                ?></p>
            <p><b>Date added: </b><?php echo date("j F, Y", strtotime($prod -> date_added));?></p>
        </div>
    </div>
</div>

<input type="button" value="Return to the previous page" onclick="history.back()">

<?php
$file = "displayproductrec.php";
$add = "InsertNewProduct.php";
$modify = "ProductModify.php";
$view = "ViewProduct.php";
echo "<center><a href='DisplaySource.php?filename=".$file."&addname=".$add."&modifyname=".$modify."&viewname=".$view."' target='_blank'> <img src = 'phpPrint/products.png' class='img-thumbnail' width='200' height='40'> </a></center>";
?>

</body>
</html>
