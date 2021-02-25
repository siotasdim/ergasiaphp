<?php 
session_start();
require_once('includes/helper_functions.php');
$page_title = 'Login';


$errors = array();
if (filter_input(INPUT_POST,'submit')) {
    require_once('mysqli_connect.php');
    if ($email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING)) {
        if (!$email = filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Tο email που δηλώσατε δεν είναι στη σωστή μορφή';
        } 
    } else {
        $errors[] = 'Ξεχάσατε να δηλώσετε email';
    } 
    if (!$pass = filter_input(INPUT_POST, "pass", FILTER_SANITIZE_STRING)) { 
        $errors[] = 'Ξεχάσατε να δηλώσετε password';
    }
    if (empty($errors)) {
        list($status, $data) = check_data($dbc, $email, $pass);
        if ($status) { 
            $id = $data['id'];
            $firstname = $data['fname'];
            $lastname = $data['lname'];
            $roleid = $data['roleid'];
            $qemail = $data['qemail'];
            $_SESSION['user_id'] = $id;
            $_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
            $_SESSION['role_id'] = $roleid;
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;
            $_SESSION['qemail'] = $qemail;
            header("Location: loggedin.php");
            exit();
        } else { 
            $errors[] = $data;
        }
    }
}
include('includes/header.php');
print_error_message($errors);
print("<h1>Login</h1>\n");
?>

<form method="post">
<p>Email: <input type="email" name ="email"></p>
<p>Password: <input type="password" name ="pass"></p>
<p><input type="submit" name ="submit" value="Login"></p>
</form>

<?php
include('includes/footer.php');
?>