<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Ανάθεση σε Καθηγητή';
include('includes/header.php');
print("<br>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {
    $currentprof = filter_input(INPUT_POST, "currentprof");
    $courseid = filter_input(INPUT_POST, "courseid");
    $classid = filter_input(INPUT_POST, "classid");
    $oldprofid = filter_input(INPUT_POST, "oldprofid");
    if ($oldprofid==null) {
        $q = "insert into teaches (idusers,idcourses,idclasses) values (?,?,?)";
        $stmt = my_mysqli_prepare($dbc, $q);
        my_mysqli_stmt_bind_param($stmt, 'iii', $currentprof, $courseid, $classid);
        my_mysqli_stmt_execute($stmt);
        if (my_mysqli_stmt_affected_rows($stmt) == 1) {
            print("<p>Έχετε αναθέσει επιτυχώς νέο καθηγητή!</p>\n");
        } else {
            print("<p>Η ανάθεση καθηγητή δεν πραγματοποιήθηκε.</p>\n");
        }
        mysqli_stmt_close($stmt); 
        mysqli_close($dbc); 
        include('includes/footer.php');
        exit(); 
    } else {
        $q = "UPDATE teaches SET idusers = ? WHERE idusers = ? and idcourses = ? and idclasses = ?";
        $stmt = my_mysqli_prepare($dbc, $q);
        my_mysqli_stmt_bind_param($stmt, 'iiii', $currentprof, $oldprofid, $courseid, $classid);
        my_mysqli_stmt_execute($stmt);
        if (my_mysqli_stmt_affected_rows($stmt) == 1) {
            print("<p>Έχετε αναθέσει επιτυχώς νέο καθηγητή!</p>\n");
        } else {
            print("<p>Η ανάθεση καθηγητή δεν πραγματοποιήθηκε.</p>\n");
        }
        mysqli_stmt_close($stmt); 
        mysqli_close($dbc); 
        include('includes/footer.php');
        exit(); 
        }
} else {
    print_access_error_exit();
}
?>