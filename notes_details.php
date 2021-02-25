<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Αναλυτικές Βαθμολογίες';
include('includes/header.php');
print("<br>");
print("<h1>Αναλυτικές Βαθμολογίες</h1>");

if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==3) {
    if (!$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) {
        if (!$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }

    if (!$course = filter_input(INPUT_GET, 'course', FILTER_VALIDATE_INT)) {
        if (!$course = filter_input(INPUT_POST, 'course', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }

    $id = $_SESSION['user_id'];

    $q = "select count(idusers) from studentsclasses where idclasses= "
    . " (select idclasses from studentsclasses where idusers=?) ";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'i', $id);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 0) { 
        print_no_class_error();
    }
    my_mysqli_stmt_bind_result($stmt, $classcounter);
    while(mysqli_stmt_fetch($stmt)) { 
        $classcounter2 = $classcounter;
    }
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);

    $q = "SELECT sum(average) FROM studentsclasses l inner join notes n on n.idusers=l.idusers "
    . " where l.idclasses=(select idclasses from studentsclasses where idusers=?) "
    . " and n.idcourses=? ";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'ii', $id, $course);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 0) { 
        print_no_class_error();
    }
    my_mysqli_stmt_bind_result($stmt, $sumaverage);
    while(mysqli_stmt_fetch($stmt)) { 
        $sumaverage2 = $sumaverage;
    }
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);

    $classavg=$sumaverage2/$classcounter2;

    $q = "SELECT c.title, n.oralnote, n.testnote, n.average from notes n "
    . "inner join courses c on c.idcourses=n.idcourses "
    . "where n.idusers=? and c.idcourses=? ";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'ii', $id, $course);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 0) { 
        print_no_course_error();
    }
    my_mysqli_stmt_bind_result($stmt, $title, $oralnote, $testnote, $average);
    print("<table>\n"); 
    print("<tr>\n"); 
    print("<th>Μάθημα</th>\n"); 
    print("<th>Προφορικά</th>\n"); 
    print("<th>Γραπτά</th>\n"); 
    print("<th>Μέσος Όρος</th>\n"); 
    print("<th>Μέσος Όρος Τάξης</th>\n"); 
    print("</tr>\n"); 
    while(mysqli_stmt_fetch($stmt)) { 
        print("<tr>\n");
        print("<td>$title</td>\n"); 
        print("<td>$oralnote</td>\n");
        print("<td>$testnote</td>\n");
        print("<td>$average</td>\n");
        print("<td>$classavg</td>\n");
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