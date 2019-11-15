<?php
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

</br></br></br></br>

<head>
    <title>Products</title>
</head>
<body>
<h1>Products</h1>
<form action= "displayproductrec.php?q=">
    <input type="search" name="q" placeholder="Search for products">
    <button type="submit">Search</button>
</form>
<button><a href=InsertNewProduct.php?Action=Add>Add a product</a></button>
<button href=MultipleProductModify.php><a href=MultipleProductModify.php>Change the price of more than one product</a></button>

<?php
if(empty($_GET)) {
    $query = "SELECT * FROM product;";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    if (!$stmt->execute()) {
        ?>
        <title>Error occurred</title>
        <center>
            <h2>Error retrieving product details</h2>
            <p>An error occurred while trying to retrieve the product details</p>
        </center>
        <?php
    }
} else {
    //searches database and displays products matching the category searched (case insensitive but must be spelt exactly the same)
    $keyword = trim($_GET["q"]);
    $query = "SELECT DISTINCT * FROM product p JOIN product_category pc ON p.product_id = pc.product_id JOIN category c ON 
    c.category_id = pc.category_id WHERE c.category_name LIKE '$keyword' GROUP BY p.product_id";
    $stmt = $dbh->prepare($query);
    if (!$stmt->execute()){
        ?>
        <title>Error occurred</title>
        <center>
            <h2>Error retrieving product details</h2>
            <p>An error occurred while trying to retrieve the product details</p>
        </center>
        <?php
    }
}

if ($stmt -> rowCount() > 0) {
?>
<table class="table table-striped table-hover" style="background-color: white;">
    <th>ID</th>
    <th>Name</th>
    <th>Description</th>
    <th>Date added</th>
    <th>Price</th>
    <th colspan="2">Options</th>
    <?php
    while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        echo '<tr><td>'.
            $row['product_id'].'</td><td>'.
            '<a href=ViewProduct.php?prodno='. $row['product_id']. '>'.$row['product_name'].'</a></td><td>'.
            $row['product_desc'].'</td><td>'.
            date("d/m/y", strtotime($row['date_added'])).'</td><td>'.
            $row['product_price'].'</td><td>'.
            '<a href=ProductModify.php?prodno='. $row['product_id']. '&Action=Update>Update</a></td><td>'.
            '<a href=ProductModify.php?prodno='. $row['product_id']. '&Action=Delete>Delete</a></td></tr>';
    }
    } else {
        echo '<br> No products match your search';
    }
    ?>
</table>


<center><h5>Please note that the source code of 'product_category' is embedded within ProductModify's display code.  It does not have its own page.</h5></center>

<center>
    <?php
    $file = "displayproductrec.php";
    $add = "InsertNewProduct.php";
    $modify = "ProductModify.php";
    $view = "ViewProduct.php";
    echo "<center><a href='DisplaySource.php?filename=".$file."&addname=".$add."&modifyname=".$modify."&viewname=".$view."' target='_blank'> <img src = 'phpPrint/products.png' class='img-thumbnail' width='200' height='40'> </a></center>";
    ?>
</center>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>

</html>
