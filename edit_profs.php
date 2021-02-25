<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Επεξεργασία Στοιχείων Καθηγητή';
include('includes/header.php');
print("<br>");
print("<h1>Επεξεργασία Στοιχείων Καθηγητή</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    if (!$profid = filter_input(INPUT_GET, 'profid', FILTER_VALIDATE_INT)) {
        if (!$profid = filter_input(INPUT_POST, 'profid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }

    if (filter_input(INPUT_POST, 'submit')) {
        $errors = array();
        if (!$firstname = filter_input(INPUT_POST, "firstname", FILTER_SANITIZE_STRING)) {
            $errors[] = 'Ξεχάσατε να δηλώσετε όνομα';
        }
    
        if (!$lastname = filter_input(INPUT_POST, "lastname", FILTER_SANITIZE_STRING)) {
            $errors[] = 'Ξεχάσατε να δηλώσετε επώνυμο';
        }
    
        if (!$phone = filter_input(INPUT_POST, "phone", FILTER_SANITIZE_STRING)) {
            $errors[] = 'Ξεχάσατε να δηλώσετε τηλέφωνο επικοινωνίας';
        }

        if ($email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING)) {
            if (!$email = filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Tο email που δηλώσατε δεν είναι στη σωστή μορφή';
            } else { 
                $q = "SELECT email FROM users WHERE email = ? AND idusers != ?";
                $stmt = my_mysqli_prepare($dbc, $q);
                my_mysqli_stmt_bind_param($stmt, 'si', $email, $profid);
                my_mysqli_stmt_execute($stmt);
                my_mysqli_stmt_store_result($stmt); 
                if (my_mysqli_stmt_num_rows($stmt) > 0) { 
                    $errors[] = 'Το email που δηλώσατε υπάρχει ήδη';
                }
                mysqli_stmt_free_result($stmt);
                mysqli_stmt_close($stmt);
            }
        } else {
            $errors[] = 'Ξεχάσατε να δηλώσετε email';
        }
    
        if (empty($errors)) {
            $q = "UPDATE users SET firstname=?, lastname=?, email=?, phone=? WHERE idusers=?";
            $stmt = my_mysqli_prepare($dbc, $q);
            my_mysqli_stmt_bind_param($stmt, 'ssssi', $firstname, $lastname, $email, $phone, $profid);
            my_mysqli_stmt_execute($stmt);
            if (my_mysqli_stmt_affected_rows($stmt) == 0) { 
                $errors[] = 'Δεν πραγματοποιήθηκε κάποια μεταβολή';
            } else {
                print("<h1>Επιτυχής αλλαγή στοιχείων χρήστη!</h1>\n");
            }
            mysqli_stmt_close($stmt);
        }
        print_error_message($errors);
        exit();
        include('includes/footer.php');
    }

    $q = "SELECT email, lastname, firstname, phone FROM users where idusers=?";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'i', $profid);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 0) { 
        print_access_error_exit();
    }
    my_mysqli_stmt_bind_result($stmt, $email, $lastname, $firstname, $phone);
    mysqli_stmt_fetch($stmt); 
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
    ?>
    <form action="" method="post">
        <p>Όνομα: <input type="text" name="firstname" value="<?php print($firstname); ?>" required></p>
        <p>Επώνυμο: <input type="text" name="lastname" value="<?php print($lastname); ?>" required></p>
        <p>Τηλέφωνο: <input type="text" name="phone" value="<?php print($phone); ?>" required></p>
        <p>E-mail: <input type="email" name="email" value="<?php print($email); ?>" required></p>
        <p><input type="submit" name="submit" value="Υποβολή Αλλαγών"></p>
        <input type="hidden" name="profid" value="<?php print($profid); ?>">
    </form>

    <?php
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>