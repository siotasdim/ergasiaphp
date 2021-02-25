<?php
session_start();
$page_title = 'Αλλαγή Στοιχείων Χρήστη';
require('includes/header.php');
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');

$id = $_SESSION['user_id'];

if (filter_input(INPUT_POST, 'submit')) {
    $errors = array();
    if ($email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING)) {
        if (!$email = filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Tο email που δηλώσατε δεν είναι στη σωστή μορφή';
        } else {  
            $q = "SELECT idusers FROM users WHERE email = ? AND idusers != ?";
            $stmt = my_mysqli_prepare($dbc, $q);
            my_mysqli_stmt_bind_param($stmt, 'si', $email, $id);
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
    if (!$curpass = filter_input(INPUT_POST, "curpass", FILTER_SANITIZE_STRING)) { 
        $errors[] = 'Ξεχάσατε να δηλώσετε τρέχων password';
    }
    if (!$first_name = filter_input(INPUT_POST, "first_name", FILTER_SANITIZE_STRING)) {
        $errors[] = 'Ξεχάσατε να δηλώσετε όνομα';
    }
    if (!$last_name = filter_input(INPUT_POST, "last_name", FILTER_SANITIZE_STRING)) {
        $errors[] = 'Ξεχάσατε να δηλώσετε επίθετο';
    }
    if (!$phone = filter_input(INPUT_POST, "phone", FILTER_SANITIZE_STRING)) {
        $errors[] = 'Ξεχάσατε να δηλώσετε τηλέφωνο επικοινωνίας';
    }

    if (!$pass1 = filter_input(INPUT_POST, "pass1", FILTER_SANITIZE_STRING)) { 
        $errors[] = 'Ξεχάσατε να δηλώσετε νέο password';
    } else {
        if (!$pass2 = filter_input(INPUT_POST, "pass2", FILTER_SANITIZE_STRING)) {
            $errors[] = 'Ξεχάσατε να δηλώσετε νέο password επιβεβαίωσης';
        } else {
            if ($pass2 != $pass1) {
                $errors[] = 'Τα password δεν ταιριάζουν';
            }
        }
    }

    if ($id == 1) {
        $errors[] = 'Τα στοιχεία του χρήστη x x δεν γίνεται να αλλαχτούν';
    }

    if (!empty($errors)) {
        print_error_message($errors);
    } else {
        $q = "UPDATE users SET password = SHA2(?, 256), firstname=?, lastname=?, email=?, phone=? WHERE idusers = $id";
        $stmt = my_mysqli_prepare($dbc, $q);
        my_mysqli_stmt_bind_param($stmt, 'sssss', $pass1, $first_name, $last_name, $email, $phone);
        my_mysqli_stmt_execute($stmt);
        if (my_mysqli_stmt_affected_rows($stmt) == 1) {
            print("<p>Έχετε αλλάξει επιτυχώς τα στοιχεία σας!</p>\n");
        } else {
            print("<p>Τα στοιχεία σας δεν άλλαξαν.</p>\n");
        }
        mysqli_stmt_close($stmt); 
        mysqli_close($dbc); 
        include('includes/footer.php');
        exit(); 
    }
}

$q = "SELECT firstname, lastname, email, phone FROM users WHERE idusers=?";
$stmt = my_mysqli_prepare($dbc, $q);
my_mysqli_stmt_bind_param($stmt, 'i', $id);
my_mysqli_stmt_execute($stmt);
my_mysqli_stmt_store_result($stmt);
if (my_mysqli_stmt_num_rows($stmt) == 0) { 
    print_access_error_exit();
}
my_mysqli_stmt_bind_result($stmt, $first_name, $last_name, $email, $phone);
mysqli_stmt_fetch($stmt); 
mysqli_stmt_free_result($stmt);
mysqli_stmt_close($stmt);
mysqli_close($dbc);
?>

<h1>Αλλαγή Στοιχείων Χρήστη</h1>
<form action="" method="post">
    <p>Όνομα: <input type="text" name="first_name" value="<?php print($first_name); ?>"></p>
    <p>Επώνυμο: <input type="text" name="last_name" value="<?php print($last_name); ?>"></p>
    <p>E-mail: <input type="email" name="email" value="<?php if(isset($email)) print($email); ?>"></p>
    <p>Κινητό Τηλέφωνο (69x): <input type="text" name="phone" value="<?php if(isset($phone)) print($phone); ?>"></p>
    <p>Τρέχων Password: <input type="password" name="curpass"></p>
    <p>Νέο Password: <input type="password" name="pass1"></p>
    <p>Επιβεβαίωση νέου Password: <input type="password" name="pass2"></p>
    <p><input type="submit" name="submit" value="Αλλαγή Στοιχείων"></p>
</form>
<?php
include('includes/footer.php');
?>