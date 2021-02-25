<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Ανάθεση Μαθήματος σε Καθηγητή';
include('includes/header.php');
print("<br>");
print("<h1>Ανάθεση Μαθήματος σε Καθηγητή</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    $noprofincourse=false;
    $errors = array();
    if (!$profid = filter_input(INPUT_GET, 'profid', FILTER_VALIDATE_INT)) {
        if (!$profid = filter_input(INPUT_POST, 'profid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }
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
//να βρουμε αν υπαρχει αλλος καθηγητης στο μαθημα
    $q = "SELECT idusers from teaches where idcourses=? and idclasses=?";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'ii', $courseid, $classid);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) > 0) {  //αμα δηλαδη το διδασκει καθηγητης
        my_mysqli_stmt_bind_result($stmt, $oldprof);
        $noprofincourse=true;   //αμα δηλαδη το διδασκει καθηγητης
        while(mysqli_stmt_fetch($stmt)) {
            $oldprof2 = $oldprof;
        }
    }
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);

    if ($noprofincourse==false) { //αμα δηλαδη το μαθημα δεν το διδασκει καθηγητης
        $q = "INSERT INTO teaches (idusers, idcourses, idclasses) VALUES (?, ?, ?)";
        $stmt = my_mysqli_prepare($dbc, $q);
        my_mysqli_stmt_bind_param($stmt, 'iii', $profid, $courseid, $classid);
        my_mysqli_stmt_execute($stmt);
        if (my_mysqli_stmt_affected_rows($stmt) == 1) {
            print("<p>Έχετε καταχωρήσει επιτυχώς το μάθημα στον καθηγητή!</p>\n");
        } else {
            print("<p>Η καταχώρηση δεν πραγματοποιήθηκε.</p>\n");
        }
        mysqli_stmt_close($stmt); 
        mysqli_close($dbc); 
        include('includes/footer.php');
        exit(); 
    } else {
        $q = "UPDATE teaches SET idusers=? WHERE idusers=? and idcourses=? and idclasses=?";
        $stmt = my_mysqli_prepare($dbc, $q);
        my_mysqli_stmt_bind_param($stmt, 'iiii', $profid, $oldprof2, $courseid, $classid);
        my_mysqli_stmt_execute($stmt);
        if (my_mysqli_stmt_affected_rows($stmt) == 1) {
            print("<p>Έχετε αλλάξει επιτυχώς καθηγητή στο μάθημα!</p>\n");
        } else {
            print("<p>Το συγκεκριμένο μάθημα είχε ήδη ανατεθεί στον συγκεκριμένο καθηγητή.</p>\n");
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