<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Ανάθεση Μαθητή σε Σειρά';
include('includes/header.php');
print("<br>");
print("<h1>Ανάθεση Μαθητή σε Σειρά</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    if (!$studentid = filter_input(INPUT_GET, 'studentid', FILTER_VALIDATE_INT)) {
        if (!$studentid = filter_input(INPUT_POST, 'studentid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }
    
    $q = "SELECT idclasses, title FROM classes ";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 0) { 
        print_access_error_exit();
    }
    my_mysqli_stmt_bind_result($stmt, $classid, $classtitle);

    print("<table>\n"); 
    print("<tr>\n");  
    print("<th class='hide'>Κωδικός Μαθήματος</th>\n"); 
    print("<th class='hide'>Κωδικός Σειράς</th>\n"); 
    print("<th>Σειρά ΣΠΗΥ</th>\n"); 
    print("<th>Ανάθεση σε Μαθητή σε Σειρά</th>\n");
    print("</tr>\n"); 

    while(mysqli_stmt_fetch($stmt)) { 
        print("<tr>\n");
        print("<td class='hide'>$studentid</td>\n");
        print("<td class='hide'>$classid</td>\n");
        print("<td>$classtitle</td>\n");
        print("<td><a href='confirm_student_to_class.php?studentid=$studentid&classid=$classid'>Ανάθεση</a></td>\n");
        print("</tr>\n");
    }
    print("</table>\n");
    print("<br>\n");
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>