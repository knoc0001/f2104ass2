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
    <title>Edit Multiple Products</title>
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
        </ul>
    </div>
</nav>

</br></br></br></br>


<?php
$query = "SELECT * FROM product";
$stmt = $dbh->prepare($query);
if (!$stmt->execute()){
    $str = "<h2>Error retrieving product details</h2><p>An error occurred while trying to retrieve the product 
        details</p>";
    commitOrRollback($dbh,'N',$str,'displayproductrec.php');
    exit();
}

//submit button has been clicked, update products with new price
if (isset($_POST['submit'])) {
    for($i=0; $i<count($_POST['id']); $i++){
        $query = "UPDATE product SET product_price = ? WHERE product_id = ?";
        $stmt = $dbh->prepare($query);
        if (!($stmt->execute(array($_POST['price'][$i], $_POST['id'][$i])))){
            $str = "<h2>Error updating products</h2><p>The following product was not successfully updated <br><b>ID:
</b>".$_POST["id"][$i]."<br> <b>Name: </b>".$_POST["name"][$i]."</p>";
            commitOrRollback($dbh, 'N', $str,'MultipleProductModify.php');
            exit();
        }}
    ?>
    <title>Successfully updated</title>
    <center>
        <h2>Successfully updated</h2>
        <p>The product prices have been successfully updated</p>
        <input type='button' value='Return to List' OnClick='window.location="displayproductrec.php"'>
    </center>
    </body>
    <?php
} else{
    ?>
    <h2>Edit Multiple Products</h2>
    <form method="post" action="MultipleProductModify.php?&Action=ConfirmUpdate">
        <table>
            <tr>
                <th>Name</th>
                <th>Price</th>
            </tr>
            <?php
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                    <input type="hidden" name="id[]" value="<?php echo $row['product_id']; ?>">
                    <input type="hidden" name="name[]" value="<?php echo $row['product_name']; ?>">
                    <td><?php echo $row['product_name']?></td>
                    <td><input type="number" name="price[]" size="10" min="0" step="0.01"
                               value="<?php echo $row['product_price']; ?>" required
                               oninvalid="this.setCustomValidity('Please enter a valid price (maximum of 2 decimal points)')"
                               oninput="this.setCustomValidity('')"></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <br>
        <input type="submit" name="submit" value="Update Products">
        <input type="button" value="Return to List" OnClick="window.location='displayproductrec.php'">
    </form>
    <?php
}
?>

<center>
    <?php
    $file = "MultipleProductModify.php";
    echo "<a href='DisplaySource2.php?filename=".$file."' target='_blank'> <img src = 'phpPrint/multipleproducts.png' class='img-thumbnail' width='200' height='40'> </a>";
    ?>


</center>

</body>
</html>
