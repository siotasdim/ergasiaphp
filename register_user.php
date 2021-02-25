<?php 
session_start();
$page_title = 'Εγγραφή';
require('includes/header.php');
require_once('includes/helper_functions.php');
print("<br>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    if (filter_input(INPUT_POST, 'submit')) {
        $errors = array();
        if (!$first_name = filter_input(INPUT_POST, "first_name", FILTER_SANITIZE_STRING)) {
            $errors[] = 'Ξεχάσατε να δηλώσετε όνομα';
        }

        if (!$last_name = filter_input(INPUT_POST, "last_name", FILTER_SANITIZE_STRING)) {
            $errors[] = 'Ξεχάσατε να δηλώσετε επώνυμο';
        }
        if ($email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING)) {
            if (!$email = filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Tο email που δηλώσατε δεν είναι στη σωστή μορφή';
            } else {          
                require_once('mysqli_connect.php');
                $q = "SELECT idusers FROM users WHERE email = ?";
                $stmt = my_mysqli_prepare($dbc, $q);
                my_mysqli_stmt_bind_param($stmt, 's', $email);
                my_mysqli_stmt_execute($stmt);
                my_mysqli_stmt_store_result($stmt);
                if (my_mysqli_stmt_num_rows($stmt) > 0) { 
                    $errors[] = 'Το email που χρησιμοποίησες υπάρχει ήδη';
                }
                mysqli_stmt_free_result($stmt);
                mysqli_stmt_close($stmt);
            }
        } else {
            $errors[] = 'Ξεχάσατε να δηλώσετε email';
        }

        if (!$pass1 = filter_input(INPUT_POST, "pass1", FILTER_SANITIZE_STRING)) { 
            $errors[] = 'Ξεχάσατε να δηλώσετε password';
        } else {
            if (!$pass2 = filter_input(INPUT_POST, "pass2", FILTER_SANITIZE_STRING)) {
                $errors[] = 'Ξεχάσατε να δηλώσετε password επιβεβαίωσης';
            } else {
                if ($pass2 != $pass1) {
                    $errors[] = 'Δεν ταιριάζουν τα passwords μεταξύ τους';
                }
            }
        }

        if (!$phone = filter_input(INPUT_POST, "phone", FILTER_SANITIZE_STRING)) { 
            $errors[] = 'Ξεχάσατε να δηλώσετε τηλέφωνο επικοινωνίας';
        } 

        $roleid =  $_POST['roleid'];
        if ($roleid == "student") { 
            $role = 3;
        } else if ($roleid == "professor") {
            $role = 2;
        } else if ($roleid == "boss") {
            $role = 1;
        }

        if (!empty($errors)) { 
            print_error_message($errors);
        } else { 
            $q = "call insert_new_user(?, ?, ?, SHA2(?, 256), ?, ?)";
            $stmt = my_mysqli_prepare($dbc, $q);
            my_mysqli_stmt_bind_param($stmt, 'ssssss', $first_name, $last_name, $email, $pass1, $phone, $role);
            my_mysqli_stmt_execute($stmt);
            if (my_mysqli_stmt_affected_rows($stmt) == 2) {
                print("<h1>Επιτυχής εγγραφή!</h1>\n");
                print("<h1>Έχετε εγγράψει τον χρήστη επιτυχώς!</h1>\n");
            }
            mysqli_stmt_close($stmt);
            mysqli_close($dbc);
            include('includes/footer.php');
            exit();
        }
    }
    ?>
    <h1>Εγγραφή</h1>
    <form action="register_user.php" method="post">
    <p>Όνομα: <input type="text" name="first_name" 
        value="<?php if(isset($first_name)) print($first_name); ?>"></p>
    <p>Επώνυμο: <input type="text" name="last_name"
        value="<?php if(isset($last_name)) print($last_name); ?>"></p>
    <p>E-mail: <input type="email" name="email"
        value="<?php if(isset($email)) print($email); ?>"></p>
    <p>Κινητό Τηλέφωνο (69x): <input type="text" name="phone"
        value="<?php if(isset($phone)) print($phone); ?>"></p>
    <p>Password: <input type="password" name="pass1"></p>
    <p>Επιβεβαίωση Password: <input type="password" name="pass2"></p>
    <p>
        <label for="role">Επιλέξτε Ρόλο: </label>
        <select required name="roleid" id="roleid">
            <option value="">Κλικ για εμφάνιση επιλογών</option>
            <option value="student">Μαθητής</option>
            <option value="professor">Καθηγητής</option>
            <option value="boss">Γραφείο Εκπαίδευσης</option>
        </select> 
    </p>
    <p><input type="submit" name="submit" value="Εγγραφή"></p>
    <input type="hidden" name="submitted" value="TRUE"> 
    </form>
    <?php
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>