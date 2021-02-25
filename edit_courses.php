<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Επεξεργασία Μαθήματος';
include('includes/header.php');
print("<br>");
print("<h1>Επεξεργασία Μαθήματος</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    if (!$courseid = filter_input(INPUT_GET, 'courseid', FILTER_VALIDATE_INT)) {
        if (!$courseid = filter_input(INPUT_POST, 'courseid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }

    if (filter_input(INPUT_POST, 'submit')) {
        $errors = array();
        if (!$title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING)) {
            $errors[] = 'Ξεχάσατε να δηλώσετε ονομασία σειράς';
        }

        if (!empty($errors)) {
            print_error_message($errors);
        } else {  
            $q = "UPDATE courses SET title = ? WHERE idcourses = ?";
            $stmt = my_mysqli_prepare($dbc, $q);
            my_mysqli_stmt_bind_param($stmt, 'si', $title, $courseid);
            my_mysqli_stmt_execute($stmt);
            if (my_mysqli_stmt_affected_rows($stmt) == 1) {
                print("<p>Έχετε καταχωρήσει επιτυχώς τις αλλαγές στο μάθημα!</p>\n");
            } else {
                print("<p>Οι αλλαγές δεν καταχωρήθηκαν.</p>\n");
            }
            mysqli_stmt_close($stmt); 
            mysqli_close($dbc); 
            include('includes/footer.php');
            exit(); 
        }
    }

    $q = "SELECT title FROM courses WHERE idcourses = ?";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'i', $courseid);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 0) { 
        print_access_error_exit();
    }
    my_mysqli_stmt_bind_result($stmt, $title);
    mysqli_stmt_fetch($stmt); 
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
    ?>
    <form action="" method="post">
        <p>Σειρά: <input type="text" name="title" value="<?php print($title); ?>" required></p>
        <p><input type="submit" name="submit" value="Υποβολή Αλλαγών"></p>
        <input type="hidden" name="courseid" value="<?php print($courseid); ?>">
    </form>

    <?php
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>