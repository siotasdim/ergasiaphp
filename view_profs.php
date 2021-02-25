<?php
session_start();
$page_title = 'Καθηγητές';
include('includes/header.php');
require_once('includes/helper_functions.php'); 
require_once('mysqli_connect.php');
print("<br>");
print("<h1>Καθηγητές</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    $q = "SELECT u.idusers, u.lastname, u.firstname, u.phone, u.email FROM users u "
            . "inner join usersroles r on r.idusers=u.idusers "
            . "where r.idroles=2 ";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $idusers, $lastname, $firstname, $phone, $email);

    print("<table>\n"); 
    print("<tr>\n"); 
    print("<th class='hide'>Κωδικός Χρήστη</th>\n"); 
    print("<th>Επώνυμο</th>\n"); 
    print("<th>Όνομα</th>\n"); 
    print("<th>Τηλέφωνο</th>\n"); 
    print("<th>e-mail</th>\n"); 
    print("<th>Επεξεργασία Στοιχείων</th>\n"); 
    print("<th>Ανάθεση Μαθήματος</th>\n");
    print("<th>Διαγραφή</th>\n");
    print("</tr>\n"); 

    while(mysqli_stmt_fetch($stmt)) { 
        print("<tr>\n");
        print("<td class='hide'>$idusers</td>\n");
        print("<td>$lastname</td>\n");
        print("<td>$firstname</td>\n");
        print("<td>$phone</td>\n");
        print("<td>$email</td>\n");
        print("<td><a href='edit_profs.php?profid=$idusers'>Επεξεργασία</a></td>\n");
        print("<td><a href='assign_prof_to_course.php?profid=$idusers'>Ανάθεση</a></td>\n");
        print("<td><a href='delete_prof.php?profid=$idusers'>Διαγραφή</a></td>\n");
        print("</tr>\n");
    }
    print("</table>\n");
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>