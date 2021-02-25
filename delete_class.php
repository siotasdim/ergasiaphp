<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Διαγραφή Σειράς';
include('includes/header.php');
print("<br>");
print("<h1>Διαγραφή Σειράς</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    if (!$classid = filter_input(INPUT_GET, 'classid', FILTER_VALIDATE_INT)) {
        if (!$classid = filter_input(INPUT_POST, 'classid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }

    if (filter_input(INPUT_POST, 'submit')) {
        $sure = (filter_input(INPUT_POST, 'sure', FILTER_SANITIZE_STRING));
        $confirm = ($sure == 'yes') ? true: false; 
        if (!$confirm) { 
            print("<p>Η σειρά ΔΕΝ διαγράφτηκε.</p>\n");
        } else { 
            $q = "DELETE from classes WHERE idclasses = ?";
            $stmt = my_mysqli_prepare($dbc, $q);
            my_mysqli_stmt_bind_param($stmt, 'i', $classid);
            my_mysqli_stmt_execute($stmt);
            if (my_mysqli_stmt_affected_rows($stmt) == 0) {
                print_access_error_exit();
            } else {
                print("<p>Η σειρά διαγράφτηκε επιτυχώς.</p>\n");
            }
            mysqli_stmt_close($stmt);
        }                           
        include('includes/footer.php');
        exit();
    }

    $q = "SELECT title FROM classes WHERE idclasses=?";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'i', $classid);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 0) { 
        print_access_error_exit();
    }
    my_mysqli_stmt_bind_result($stmt, $classtitle);
    mysqli_stmt_fetch($stmt); 
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
    ?>
    <form action="" method="post">
        <h4>Σειρά: <?php print("$classtitle\n");?></h4>
        <p>Είστε σίγουροι για την διαγραφή της σειράς;<br>
        <input type="radio" name="sure" value="yes"> Ναι
        <input type="radio" name="sure" value="no"> Όχι</p>
        <p><input type="submit" name="submit" value="Υποβολή"></p>
        <input type="hidden" name="classid" value="<?php print($classid);?>">
    </form>
    <?php 
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>