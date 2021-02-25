<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Διαγραφή Καθηγητή';
include('includes/header.php');
print("<br>");
print("<h1>Διαγραφή Καθηγητή</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    if (!$idusers = filter_input(INPUT_GET, 'profid', FILTER_VALIDATE_INT)) {
        if (!$idusers = filter_input(INPUT_POST, 'profid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }

    if (filter_input(INPUT_POST, 'submit')) {
        $sure = (filter_input(INPUT_POST, 'sure', FILTER_SANITIZE_STRING));
        $confirm = ($sure == 'yes') ? true: false; 
        if (!$confirm) { 
            print("<p>Ο καθηγητής ΔΕΝ διαγράφτηκε.</p>\n");
        } else { 
            $q = "DELETE from users WHERE idusers=?";
            $stmt = my_mysqli_prepare($dbc, $q);
            my_mysqli_stmt_bind_param($stmt, 'i', $idusers);
            my_mysqli_stmt_execute($stmt);
            if (my_mysqli_stmt_affected_rows($stmt) == 0) {
                print_access_error_exit();
            } else {
                print("<p>Ο καθηγητής διαγράφτηκε επιτυχώς.</p>\n");
            }
            mysqli_stmt_close($stmt);
        }                           
        include('includes/footer.php');
        exit();
    }

    $q = "SELECT firstname, lastname FROM users WHERE idusers=?";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'i', $idusers);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 0) { 
        print_access_error_exit();
    }
    my_mysqli_stmt_bind_result($stmt, $firstname, $lastname);
    mysqli_stmt_fetch($stmt); 
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
    ?>
    <form action="" method="post">
        <h4>Όνομα: <?php print("$firstname $lastname\n");?></h4>
        <p>Είστε σίγουροι για την διαγραφή του χρήστη;<br>
        <input type="radio" name="sure" value="yes"> Ναι
        <input type="radio" name="sure" value="no"> Όχι</p>
        <p><input type="submit" name="submit" value="Υποβολή"></p>
        <input type="hidden" name="profid" value="<?php print($idusers);?>">
    </form>
    <?php 
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>