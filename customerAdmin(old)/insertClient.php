<html>

<h1>Database Manipulation Test</h1>

<h3>
    <a href="updateClients.html">Update a Client</a>
    <a href="insertClient.php">Insert a Client</a>
    <a href="modifyClient.php">Modify a Client</a>
    <a href="showDB.php">Show Database</a>
</h3>

<?php
if (empty($_POST["inputName"]))
{
    ?>
    <form method="post" action="insertClient.php">
        <input type="text" size="20" name="inputName">
        <input type="submit" value="submit">
    </form>
    <?php
}
else
{
    $query = "INSERT INTO client (clientName) VALUES ('$_POST[inputName]')";
    $Host= "130.194.7.82";
    $DB = "s29726115";
    $dsn= "mysql:host=$Host;dbname=$DB";
    $UName = "s29726115";
    $PWord = "monash00";
    $dbh = new PDO($dsn,$UName,$PWord);
    $stmt= $dbh->prepare($query);
    $stmt->execute();
    ?>
    <meta http-equiv="Refresh" content="5; url=showDB.php">
    <?php
}
?>
</html>