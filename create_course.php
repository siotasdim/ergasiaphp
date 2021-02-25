<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Δημιουργία Νέου Μαθήματος';
include('includes/header.php');
print("<br>");
print("<br>");
print("<br>");
print("<h1>Δημιουργία Νέου Μαθήματος</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    if (filter_input(INPUT_POST, 'submit')) {
        $errors = array();
        if (!$title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING)) {
            $errors[] = 'Ξεχάσατε να δηλώσετε ονομασία σειράς';
        }

        if (!empty($errors)) {
            print_error_message($errors);
        } else {  
            $q = "INSERT INTO courses (title) VALUES (?)";
            $stmt = my_mysqli_prepare($dbc, $q);
            my_mysqli_stmt_bind_param($stmt, 's', $title);
            my_mysqli_stmt_execute($stmt);
            if (my_mysqli_stmt_affected_rows($stmt) == 1) {
                print("<p>Έχετε καταχωρήσει επιτυχώς νέο μάθημα!</p>\n");
                print("<p><a href='create_course.php'>Δημιουργία Νέου Μαθήματος</a></p>\n");
            } else {
                print("<p>Το νέο μάθημα δεν καταχωρήθηκε.</p>\n");
            }
            mysqli_stmt_close($stmt); 
            mysqli_close($dbc); 
            include('includes/footer.php');
            exit(); 
        }
    }

    ?>
    <form action="" method="post">
        <p>Ονομασία Μαθήματος: <input type="text" name="title" value="" required></p>
        <p><input type="submit" name="submit" value="Υποβολή"></p>
    </form>

    <?php
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>