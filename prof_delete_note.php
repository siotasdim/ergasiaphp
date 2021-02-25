<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Διαγραφή Βαθμολογίας';
include('includes/header.php');
print("<br>");
print("<h1>Διαγραφή Βαθμολογίας</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==2) {

    if (!$studentid = filter_input(INPUT_GET, 'studentid', FILTER_VALIDATE_INT)) {
        if (!$studentid = filter_input(INPUT_POST, 'studentid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }

    if (!$courseid = filter_input(INPUT_GET, 'courseid', FILTER_VALIDATE_INT)) {
        if (!$courseid = filter_input(INPUT_POST, 'courseid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }

    if (filter_input(INPUT_POST, 'submit')) {
        $sure = (filter_input(INPUT_POST, 'sure', FILTER_SANITIZE_STRING));
        $confirm = ($sure == 'yes') ? true: false; 
        if (!$confirm) { 
            print("<p>Η βαθμολογία ΔΕΝ διαγράφτηκε.</p>\n");
        } else { 
            $q = "DELETE from notes WHERE idusers = ? and idcourses = ?";
            $stmt = my_mysqli_prepare($dbc, $q);
            my_mysqli_stmt_bind_param($stmt, 'ii', $studentid, $courseid);
            my_mysqli_stmt_execute($stmt);
            if (my_mysqli_stmt_affected_rows($stmt) == 0) {
                print_access_error_exit();
            } else {
                print("<p>Η βαθμολογία διαγράφτηκε επιτυχώς.</p>\n");
            }
            mysqli_stmt_close($stmt);
        }                           
        include('includes/footer.php');
        exit();
    }

    $q = "SELECT u.lastname, u.firstname, c.title from users u
            inner join notes n on n.idusers=u.idusers
            inner join courses c on c.idcourses=n.idcourses
            where u.idusers = ? and c.idcourses = ?";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'ii', $studentid, $courseid);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 0) { 
        print_access_error_exit();
    }
    my_mysqli_stmt_bind_result($stmt, $lastname, $firstname, $coursetitle);
    mysqli_stmt_fetch($stmt); 
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
    ?>
    <form action="" method="post">
        <p>Είστε σίγουροι για την διαγραφή της βαθμολογίας του μαθητή <h4><?php print("$firstname $lastname\n");?></h4>
        στο μάθημα <h4><?php print("$coursetitle ");?></h4>;<br>
        <input type="radio" name="sure" value="yes"> Ναι
        <input type="radio" name="sure" value="no"> Όχι</p>
        <p><input type="submit" name="submit" value="Υποβολή"></p>
        <input type="hidden" name="studentid" value="<?php print($studentid);?>">
        <input type="hidden" name="courseid" value="<?php print($courseid);?>">
    </form>
    <?php 
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>