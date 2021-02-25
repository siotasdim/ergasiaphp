<?php

function print_system_error()
{
    print("<h1>Σφάλμα Συστήματος</h1>\n");
    print("<p class='error'>Δεν ήταν δυνατή η ενέργεια αυτή λόγω σφάλματος συστήματος. "
        . "Παρακαλώ δοκιμάστε αργότερα.</p>\n");
}

function print_error_message($errors)
{
    if (!empty($errors)) {
        print("<h1>Σφάλμα</h1>\n");
        print("<p class='error'>Ανιχνεύθηκαν τα εξής σφάλματα:<br>\n");
        foreach ($errors as $message) {
            print(" - $message!<br>\n");
        }
        print("<p>Παρακαλώ ξαναδοκιμάστε!</p>\n");
    }
}

function print_access_error_exit()
{
    print("<p class='error'>Δεν επιτρέπεται η πρόσβαση.</p>\n");
    include('includes/footer.php');
    exit();
}

function print_no_class_error()
{
    print("<p class='error'>Η συγκεκριμένη σειρά είναι κενή.</p>\n");
    include('includes/footer.php');
    exit();
}

function print_no_course_error()
{
    print("<p class='error'>Δεν έχετε εγγραφεί στο συγκεκριμένο μάθημα.</p>\n");
    include('includes/footer.php');
    exit();
}

function my_mysqli_prepare($dbc, $q)
{
    if (!$stmt = mysqli_prepare($dbc, $q)) {
        print_system_error();
        die('stmt prepare() failed: ' . mysqli_error($dbc));
    }
    return $stmt; //επιστρέφει true/false
}

function my_mysqli_stmt_bind_param($stmt, $type, ...$params)
{
    //βάζουμε ...$params όταν δεν ξέρουμε ακριβώς πόσες μεταβλητές θα δώσουμε σαν input
    if (!$r = mysqli_stmt_bind_param($stmt, $type, ...$params)) {
        print_system_error();
        die('stmt bind_param() failed: ' . mysqli_stmt_error($stmt));
    }
    return $r; //επιστρέφει true/false
    //εμείς εδω δεν το χρησιμοποιούμε κάπου το r = results
}

function my_mysqli_stmt_execute($stmt)
{
    if (!$r = mysqli_stmt_execute($stmt)) {
        print_system_error();
        die('stmt execute() failed: ' . mysqli_stmt_error($stmt));
    }
    return $r;//επιστρέφει true/false 
}

function my_mysqli_stmt_store_result($stmt)
{
    if (!$r = mysqli_stmt_store_result($stmt)) {
        print_system_error();
        die('stmt store result() failed: ' . mysqli_stmt_store_result($stmt));
    }
    return $r;//επιστρέφει true/false 
}

function my_mysqli_stmt_bind_result($stmt, &...$results)
{
    //με το & δηλώνουμε ότι στο results οι τιμές μπορούν να αλλάζουν
    //βάζουμε τις ... όταν δεν ξέρουμε ακριβώς πόσες μεταβλητές θα πάρουμε σαν output
    if (!$r = mysqli_stmt_bind_result($stmt, ...$results)) {
        print_system_error();
        die('stmt bind result() failed: ' . mysqli_stmt_error($stmt));
    }
    return $r; //επιστρέφει true/false 
    //εμείς εδω δεν το χρησιμοποιούμε κάπου το r = results
}

function my_mysqli_stmt_fetch($stmt)
{
    $r = mysqli_stmt_fetch($stmt); //επιστρέφει true/false/null 
    if ($r === false) { //μόνο σε false θα βγάζει το μήνυμα λάθους, στο null το εκτελεί κανονικά
        print_system_error();
        die('stmt fetch() failed: ' . mysqli_stmt_error($stmt));
    }
    return $r;
    //εμείς εδω δεν το χρησιμοποιούμε κάπου το r = results
}

function my_mysqli_stmt_num_rows($stmt)
{
    $r = mysqli_stmt_num_rows($stmt);//integer, πόσες σειρές ήρθαν σαν αποτέλεσμα
    if ($r < 0) { ////πάντα >=0, ουσιαστικά εδώ δεν κάνει τίποτα η my_mysqli_stmt_num_rows
        print_system_error();
        die('stmt num rows() failed: ' . mysqli_stmt_error($stmt));
    }
    return $r;//integer, πόσες σειρές ήρθαν σαν αποτέλεσμα
}

function my_mysqli_stmt_affected_rows($stmt)
{
    $r = mysqli_stmt_affected_rows($stmt);
    if ($r === false) {
        print_system_error();
        die('stmt affected rows() failed: ' . mysqli_stmt_error($stmt));
    }
    return $r;
}

function check_data($dbc, $email, $pass) {
    $q = "SELECT u.idusers, u.firstname, u.lastname, u.email, r.idroles FROM users u inner join usersroles r on r.idusers = u.idusers WHERE u.email=? AND u.password=SHA2(?, 256)";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'ss', $email, $pass);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) == 1) {  
        my_mysqli_stmt_bind_result($stmt, $id, $firstname, $lastname, $qemail, $roleid);
        my_mysqli_stmt_fetch($stmt);
        $status = true;
        $data = array('id' => $id, 'fname' => $firstname, 'lname' => $lastname, 'qemail' => $qemail, 'roleid'=> $roleid);
    } else { 
        $msg = 'Δεν ταιριάζει το ζεύγος email, password με τα υπάρχοντα';
        $status = false;
        $data = $msg;
    }
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
    return array($status, $data);
}

function check_session() {
    if (!isset($_SESSION['user_id']) OR (!isset($_SESSION['agent'])) OR
            ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))  {
        header("Location: login.php");
        exit();
    }
}

?>