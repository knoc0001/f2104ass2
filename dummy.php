<?php
ob_start();
session_start();
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
    header("Location: login.php?Action=Login&From=".urlencode($_SERVER['HTTP_HOST'].
            $_SERVER["REQUEST_URI"]));
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

?>

<html>
<head>
    <title>Modify Product</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<center><h3>Customer Modification</h3></center>
<?php
$Host= "130.194.7.82";
$DB = "s29668050";
$UName = "s29668050";
$PWord = "monash00";
$dsn= "mysql:host=$Host;dbname=$DB";
$dbh = new PDO($dsn,$UName,$PWord);
$query="SELECT * FROM product";
$stmt = $dbh->prepare($query);
$stmt->execute();
$row=$stmt->fetchObject();

$strAction = $_GET["Action"];

switch($strAction)
{
    case "Add":
        ?>
        <form method="post" enctype="multipart/form-data" action="InsertNewProduct.php?Action=ConfirmInsert">
            <center>Insert New Product<br /></center><p />
            <table align="center" cellpadding="3">
                <tr>
                    <td><b>Prod. Name</b></td>
                    <td><input type="text" name="name" size="30" value=""></td>
                </tr>
                <tr>
                    <td><b>Prod. Description</b></td>
                    <td><input type="text" name="desc" size="30" value=""></td>
                </tr>
                <tr>
                    <td><b>Date added</b></td>
                    <td><input type="text" name="date" size="30" value="<?php echo date('Y-m-d H:i:s') ?>" </td>
                </tr>
                <tr>
                    <td><b>Prod. Price</b></td>
                    <td><input type="text" name="price" size="20" value=""></td>
                </tr>
                <tr>
                    <td><b>Category</b></td>
                    <?php
                    $checked_category = array();
                    $stmt = $dbh->prepare("SELECT category_id, category_name FROM category ORDER BY 
                    category_name");
                    $stmt -> execute();
                    while ($category_list = $stmt->fetchobject()) {
                        echo "<td>".$category_list->category_name;
                        ?>
                        <input type="checkbox" name="category_array[]" value="<?php echo ($category_list->category_id); ?>"
                            <?php echo matchString($category_list->category_id, $checked_category); ?>></td>
                        <?php
                    }
                    ?>
                </tr>

            </table>

            <table border="0">
                <tr>
                    <td><b>Select files to upload:</b><br>
                        <input type="file" size="50" name="files[]" multiple>
                    </td>
                </tr>
            </table>

            <br/>
            <table align="center">
                <tr>
                    <td><input type="submit" value="Insert Product"></td>
                    <td><input type="button" value="Return to List" OnClick="window.location='displayproductrec.php'"></td>
                </tr>
            </table>
        </form>
        <?php
        break;


    case "ConfirmInsert":
        $insert1_query = "INSERT INTO product(product_name, product_desc, date_added, product_price) 
        VALUES ('$_POST[name]', '$_POST[desc]', '$_POST[date]', '$_POST[price]')";

        $stmt = $dbh->prepare($insert1_query);
        $stmt->execute();

        $conn = mysqli_connect("130.194.7.82", "s29668050", "monash00", "s29668050");
        $testing = "SELECT product_id FROM TABLE product ORDER BY product_id DESC LIMIT 1";
        $prodNo = mysqli_query($conn, $testing);
        $smtv = mysqli_fetch_assoc($prodNo);

        foreach ($_POST["category_array"] as $selected) {
            echo $selected;
            $insert_query = "INSERT INTO product_category VALUES (this.product_id,$selected)";
            $stmt = $dbh -> prepare($insert_query);
            $stmt -> execute();
        }


        $targetDir = "product_images/";
        $allowTypes = array('jpg','png','jpeg');

        $statusMsg = $errorMsg = $insertValuesSQL = $errorUpload = $errorUploadType = '';

        if(!empty(array_filter($_FILES['files']['name']))){
            foreach($_FILES['files']['name'] as $key=>$val){
                // File upload path
                $fileName = basename($_FILES['files']['name'][$key]);
                $targetFilePath = $targetDir . $fileName;

                // Check whether file type is valid
                $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
                if(in_array($fileType, $allowTypes)){
                    // Upload file to server
                    if(move_uploaded_file($_FILES["files"]["tmp_name"][$key], $targetFilePath)){
                        // Image db insert sql
                        $insertValuesSQL .= "('".$fileName."'),";
                    }else{
                        $errorUpload .= $_FILES['files']['name'][$key].', ';
                    }
                }else{
                    $errorUploadType .= $_FILES['files']['name'][$key].', ';
                }
            }

            if(!empty($insertValuesSQL)){
                $insertValuesSQL = trim($insertValuesSQL,',');
                // Insert image file name into database
                $insert = $conn->query("INSERT INTO prod_img (product_id, product_img) VALUES ($_GET[prodNo], $insertValuesSQL");
                if($insert){
                    $errorUpload = !empty($errorUpload)?'Upload Error: '.$errorUpload:'';
                    $errorUploadType = !empty($errorUploadType)?'File Type Error: '.$errorUploadType:'';
                    $errorMsg = !empty($errorUpload)?'<br/>'.$errorUpload.'<br/>'.$errorUploadType:'<br/>'.$errorUploadType;
                    $statusMsg = "Files are uploaded successfully.".$errorMsg;
                }else{
                    $statusMsg = "Sorry, there was an error uploading your file.";
                }
            }
        }
        else{
            $statusMsg = 'Please select a file to upload.';
        }

    // Display status message
    echo $statusMsg;

        /*
        $upfile = "product_images/".$_FILES["userfile"]["name"];
        $imageFileType = strtolower(pathinfo($upfile,PATHINFO_EXTENSION));

        if(!move_uploaded_file($_FILES["userfile"]["tmp_name"],$upfile))
        {
            echo "ERROR: Could Not Move File into Directory";
        }
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            echo "ERROR: Only JPG, JPEG & PNG files are allowed.";
        }
        else {
            echo "Temporary File Name: " . $_FILES["userfile"] ["tmp_name"] . "<br />";
            echo "File Name: " . $_FILES["userfile"]["name"] . "<br />";
            echo "File Size: " . $_FILES["userfile"]["size"] . "<br />";
            echo "File Type: " . $_FILES["userfile"]["type"] . "<br />";
        }
        */

        $insert_query2 = "INSERT INTO prodimg_prod VALUES ($testing, $_FILES[userfile][name])";
        $stmt = $dbh -> prepare($insert_query2);
        $stmt -> execute();

        echo "<center>Update has been completed.</center>";
        echo "<center><input type='button' value='Return to List' OnClick='window.location=\"displayproductrec.php\"'></center>";


        break;
}

$stmt->closeCursor()
?>

</body>
</html>