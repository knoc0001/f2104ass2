<html>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

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
$file = $_GET["filename"];
$add = $_GET["addname"];
$modify = $_GET["modifyname"];
$view = $_GET["viewname"];

echo "<h1>Source Code for: ".$file."</h1>";
$fp = fopen($file, "r");
while (!feof ($fp))
{
    $trans = get_html_translation_table(HTML_ENTITIES);
    $line = fgets($fp);
    $line = strtr($line,$trans);
    $line = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",$line);
    echo $line."<br />";
}
fclose ($fp);

echo "<h1>Source Code for: ".$add."</h1>";
$ap = fopen($add, "r");
while (!feof ($ap))
{
    $trans = get_html_translation_table(HTML_ENTITIES);
    $line = fgets($ap);
    $line = strtr($line,$trans);
    $line = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",$line);
    echo $line."<br />";
}
fclose ($ap);

echo "<h1>Source Code for: ".$modify."</h1>";
$mp = fopen($modify, "r");
while (!feof ($mp))
{
    $trans = get_html_translation_table(HTML_ENTITIES);
    $line = fgets($mp);
    $line = strtr($line,$trans);
    $line = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",$line);
    echo $line."<br />";
}
fclose ($mp);

echo "<h1>Source Code for: ".$view."</h1>";
$vp = fopen($view, "r");
while (!feof ($vp))
{
    $trans = get_html_translation_table(HTML_ENTITIES);
    $line = fgets($vp);
    $line = strtr($line,$trans);
    $line = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",$line);
    echo $line."<br />";
}
fclose ($vp);
?>


</html>