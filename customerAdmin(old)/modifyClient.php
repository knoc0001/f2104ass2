<html>

<h1>Delete Customer</h1>

<h3>
    <a href="insertClient.php">Insert a Client</a>
    <a href="modifyClient.php">Modify a Client</a>
    <a href="showDB.php">Show Database</a>
</h3>

<?php
echo "<table id='data' border =1 style='border-collapse: collapse'>";
?>
<tr>
    <td><?php echo "ID"; ?></td>
    <td><?php echo "Name"; ?></td>
    <td><?php echo "Options"; ?></td>
</tr>

<?php
$conn = mysqli_connect("130.194.7.82", "s29726115", "monash00", "s29726115");
$query = "SELECT * FROM client";
$result = mysqli_query($conn, $query);

while ($row = $result->fetch_object())
{ ?>
    <tr>
        <td><?php echo $row->clientID; ?></td>
        <td><?php echo $row->clientName; ?></td>
        <td>
            <a href="custModify.php?clientID= <?php echo
            $row->clientID; ?> &Action=Update">Update</a>
            <a href="custModify.php?clientID= <?php echo
            $row->clientID; ?> &Action=Delete">Delete</a>
        </td>
    </tr>
    <?php
}
echo "</table>";
?>

</html>