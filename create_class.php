<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Δημιουργία Νέας Σειράς';
include('includes/header.php');
print("<br>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

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
            $q = "INSERT INTO classes (title, startdate, enddate) VALUES (?, ?, ?)";
            $stmt = my_mysqli_prepare($dbc, $q);
            my_mysqli_stmt_bind_param($stmt, 'sss', $title, $startdate, $enddate);
            my_mysqli_stmt_execute($stmt);
            if (my_mysqli_stmt_affected_rows($stmt) == 1) {
                print("<p>Έχετε καταχωρήσει επιτυχώς νέα σειρά!</p>\n");
            } else {
                print("<p>Η σειρά δεν καταχωρήθηκε.</p>\n");
            }
            mysqli_stmt_close($stmt); 
            mysqli_close($dbc); 
            include('includes/footer.php');
            exit(); 
        }
    }

    ?>
    <form action="" method="post">
        <p>Σειρά: <input type="text" name="title" value="" required></p>
        <p>Ημερομηνία Έναρξης: <input type="date" name="startdate" value="" placeholder="yyyy-mm-dd" required></p>
        <p>Ημερομηνία Λήξης: <input type="date" name="enddate" value="" placeholder="yyyy-mm-dd" required></p>
        <p><input type="submit" name="submit" value="Υποβολή"></p>
    </form>

    <?php
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>