<?php
function commitOrRollback($conn, $mode, $msg, $link){
    //if $mode is C commit changes, if R rollback changes Show message with button linking back to relevant page.
    if ($mode === "C") {
        $query = "COMMIT;";
        $stmt = $conn -> prepare($query);
        $stmt->execute();
    } elseif ($mode === "R") {
        $query = "ROLLBACK;";
        $stmt = $conn -> prepare($query);
        $stmt->execute();
    }
    echo "<center>$msg</center>";
    echo "<center><input type='button' value='Return to List' onclick=\"window.location.href='$link'\"></center>";
}