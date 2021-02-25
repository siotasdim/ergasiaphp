<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Ανάθεση Μαθήματος σε Σειρά';
include('includes/header.php');
print("<br>");
print("<h1>Ανάθεση Μαθήματος σε Σειρά</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    $errors = array();
    if (!$courseid = filter_input(INPUT_GET, 'courseid', FILTER_VALIDATE_INT)) {
        if (!$courseid = filter_input(INPUT_POST, 'courseid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }
    if (!$classid = filter_input(INPUT_GET, 'classid', FILTER_VALIDATE_INT)) {
        if (!$classid = filter_input(INPUT_POST, 'classid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }

    $q = "SELECT idclasses FROM classescourses where idcourses=? and idclasses=?";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'ii', $courseid, $classid);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) > 0) { 
            $errors[] = 'Το μάθημα έχει ήδη καταχωρηθεί στην συγκεκριμένη σειρά';
    }
    //να γινεται UPDATE στον πινακα classescourses ????????????????
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);

    if (!empty($errors)) {
        print_error_message($errors);
    } else {  
        $q = "INSERT INTO classescourses (idclasses, idcourses) VALUES (?, ?)";
        $stmt = my_mysqli_prepare($dbc, $q);
        my_mysqli_stmt_bind_param($stmt, 'ii', $classid, $courseid);
        my_mysqli_stmt_execute($stmt);
        if (my_mysqli_stmt_affected_rows($stmt) == 1) {
            print("<p>Έχετε καταχωρήσει επιτυχώς το μάθημα στη σειρά!</p>\n");
        } else {
            print("<p>Η καταχώρηση δεν πραγματοποιήθηκε.</p>\n");
        }
        mysqli_stmt_close($stmt); 
        mysqli_close($dbc); 
        include('includes/footer.php');
        exit(); 
    }
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>