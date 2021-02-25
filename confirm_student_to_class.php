<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Ανάθεση Μαθητή σε Σειρά ΣΠΗΥ';
include('includes/header.php');
print("<br>");
print("<h1>Ανάθεση Μαθητή σε Σειρά ΣΠΗΥ</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    $nostudentinclass=false;
    $errors = array();
    if (!$studentid = filter_input(INPUT_GET, 'studentid', FILTER_VALIDATE_INT)) {
        if (!$studentid = filter_input(INPUT_POST, 'studentid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }
    if (!$classid = filter_input(INPUT_GET, 'classid', FILTER_VALIDATE_INT)) {
        if (!$classid = filter_input(INPUT_POST, 'classid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }
//να βρουμε αν υπαρχει αλλος καθηγητης στο μαθημα
    $q = "SELECT idclasses from studentsclasses where idusers=? ";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'i', $studentid);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) > 0) {  //αμα δηλαδη o μαθητης εχει καταχωρηθει ηδη σε ταξη (καθε μαθητης φοιτα 1 φορά)
        my_mysqli_stmt_bind_result($stmt, $oldclassid);
        $nostudentinclass=true;   //αμα δηλαδη o μαθητης εχει καταχωρηθει ηδη σε ταξη (καθε μαθητης φοιτα 1 φορά)
        while(mysqli_stmt_fetch($stmt)) {
            $oldclassid2 = $oldclassid;
        }
    }
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);

    if ($nostudentinclass==false) { //αμα δηλαδη o μαθητης ΔΕΝ εχει καταχωρηθει σε ταξη
        $q = "INSERT INTO studentsclasses (idclasses, idusers) VALUES (?, ?)";
        $stmt = my_mysqli_prepare($dbc, $q);
        my_mysqli_stmt_bind_param($stmt, 'ii', $classid, $studentid);
        my_mysqli_stmt_execute($stmt);
        if (my_mysqli_stmt_affected_rows($stmt) == 1) {
            print("<p>Έχετε καταχωρήσει επιτυχώς τον μαθητή στη σειρά!</p>\n");
        } else {
            print("<p>Η καταχώρηση δεν πραγματοποιήθηκε.</p>\n");
        }
        mysqli_stmt_close($stmt); 

        mysqli_close($dbc); 
        include('includes/footer.php');
        exit(); 
    } else {
        $q = "UPDATE studentsclasses SET idclasses=? WHERE idusers=? and idclasses=?";
        $stmt = my_mysqli_prepare($dbc, $q);
        my_mysqli_stmt_bind_param($stmt, 'iii', $classid, $studentid, $oldclassid2);
        my_mysqli_stmt_execute($stmt);
        if (my_mysqli_stmt_affected_rows($stmt) == 1) {
            print("<p>Έχετε αλλάξει επιτυχώς σειρά στον μαθητή στο μάθημα!</p>\n");
        } else {
            print("<p>Ο συγκεκριμένος μαθητής είναι ήδη καταχωρημένος στη συγκεκριμένη σειρά.</p>\n");
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