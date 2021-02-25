<?php
session_start();
$page_title = 'Βαθμολογίες Μαθητών';
include('includes/header.php');
require_once('includes/helper_functions.php'); 
require_once('mysqli_connect.php');
print("<br>");
print("<h1>Βαθμολογίες Μαθητών</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    $q = "SELECT l.title, c.title, u.lastname, u.firstname, n.oralnote, n.testnote, n.average
                from notes n inner join courses c on c.idcourses=n.idcourses
                inner join studentsclasses cs on cs.idusers=n.idusers
                inner join classes l on l.idclasses=cs.idclasses
                inner join users u on u.idusers=n.idusers";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $classtitle, $coursetitle, $lastname, $firstname, $oralnote, $testnote, $average);

    print("<table>\n"); 
    print("<tr>\n"); 
    print("<th>Σειρά ΣΠΗΥ</th>\n"); 
    print("<th>Μάθημα</th>\n"); 
    print("<th>Επώνυμο</th>\n"); 
    print("<th>Όνομα</th>\n"); 
    print("<th>Προφορικός Βαθμός</th>\n"); 
    print("<th>Γραπτή Εξέταση</th>\n"); 
    print("<th>Μέσος Όρος Μαθητή</th>\n"); 
    print("</tr>\n"); 

    while(mysqli_stmt_fetch($stmt)) { 
        print("<tr>\n");
        print("<td>$classtitle</td>\n");
        print("<td>$coursetitle</td>\n");
        print("<td>$lastname</td>\n");
        print("<td>$firstname</td>\n");
        print("<td>$oralnote</td>\n");
        print("<td>$testnote</td>\n");
        print("<td>$average</td>\n");
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