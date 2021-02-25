<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Ανάθεση Μαθήματος σε Καθηγητή';
include('includes/header.php');
print("<br>");
print("<h1>Ανάθεση Μαθήματος σε Καθηγητή</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {

    if (!$profid = filter_input(INPUT_GET, 'profid', FILTER_VALIDATE_INT)) {
        if (!$profid = filter_input(INPUT_POST, 'profid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }
    
    $q = "SELECT l.idclasses, l.title, c.idcourses, c.title FROM classescourses cs
            inner join classes l on l.idclasses=cs.idclasses
            inner join courses c on c.idcourses=cs.idcourses";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 0) { 
        print_access_error_exit();
    }
    my_mysqli_stmt_bind_result($stmt, $classid, $classtitle, $courseid, $coursetitle);

    print("<table>\n"); 
    print("<tr>\n");  
    print("<th class='hide'>Κωδικός Μαθήματος</th>\n"); 
    print("<th>Μάθημα</th>\n"); 
    print("<th class='hide'>Κωδικός Σειράς</th>\n"); 
    print("<th>Σειρά ΣΠΗΥ</th>\n"); 
    print("<th>Ανάθεση σε Μαθήματος σε Σειρά</th>\n");
    print("</tr>\n"); 

    while(mysqli_stmt_fetch($stmt)) { 
        print("<tr>\n");
        print("<td class='hide'>$courseid</td>\n");
        print("<td>$coursetitle</th>\n"); 
        print("<td class='hide'>$classid</td>\n");
        print("<td>$classtitle</td>\n");
        print("<td><a href='confirm_prof_to_course.php?profid=$profid&courseid=$courseid&classid=$classid'>Ανάθεση</a></td>\n");
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