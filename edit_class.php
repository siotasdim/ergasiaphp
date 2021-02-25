<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Επεξεργασία Σειράς';
include('includes/header.php');
print("<br>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    if (!$classid = filter_input(INPUT_GET, 'classid', FILTER_VALIDATE_INT)) {
        if (!$classid = filter_input(INPUT_POST, 'classid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }

    if (filter_input(INPUT_POST, 'submit')) {
        $errors = array();
        if (!$title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING)) {
            $errors[] = 'Ξεχάσατε να δηλώσετε ονομασία σειράς';
        }
        if (!$startdate = filter_input(INPUT_POST, "startdate", FILTER_SANITIZE_STRING)) {
            $errors[] = 'Ξεχάσατε να δηλώσετε ημερομηνία έναρξης';
        }
        if (!$enddate = filter_input(INPUT_POST, "enddate", FILTER_SANITIZE_STRING)) {
            $errors[] = 'Ξεχάσατε να δηλώσετε ημερομηνία λήξης';
        }

        if (!empty($errors)) {
            print_error_message($errors);
        } else {  
            $q = "UPDATE classes SET title = ?, startdate = ?, enddate = ? WHERE idclasses = ?";
            $stmt = my_mysqli_prepare($dbc, $q);
            my_mysqli_stmt_bind_param($stmt, 'sssi', $title, $startdate, $enddate, $classid);
            my_mysqli_stmt_execute($stmt);
            if (my_mysqli_stmt_affected_rows($stmt) == 1) {
                print("<p>Έχετε καταχωρήσει επιτυχώς τις αλλαγές στην σειρά!</p>\n");
            } else {
                print("<p>Οι αλλαγές δεν καταχωρήθηκαν.</p>\n");
            }
            mysqli_stmt_close($stmt); 
            mysqli_close($dbc); 
            include('includes/footer.php');
            exit(); 
        }
    }

    $q = "SELECT title, startdate, enddate FROM classes WHERE idclasses = ?";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'i', $classid);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 0) { 
        print_access_error_exit();
    }
    my_mysqli_stmt_bind_result($stmt, $title, $startdate, $enddate);
    mysqli_stmt_fetch($stmt); 
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
    ?>
    <form action="" method="post">
        <p>Σειρά: <input type="text" name="title" value="<?php print($title); ?>" required></p>
        <p>Ημερομηνία Έναρξης: <input type="date" name="startdate" value="<?php print($startdate); ?>" placeholder="yyyy-mm-dd" required></p>
        <p>Ημερομηνία Λήξης: <input type="date" name="enddate" value="<?php print($enddate); ?>" placeholder="yyyy-mm-dd" required></p>
        <p><input type="submit" name="submit" value="Υποβολή Αλλαγών"></p>
        <input type="hidden" name="classid" value="<?php print($classid); ?>">
    </form>

    <?php
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>