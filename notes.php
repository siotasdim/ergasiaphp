<?php
session_start();
$page_title = 'Μαθήματα';
include('includes/header.php');
require_once('includes/helper_functions.php'); 
require_once('mysqli_connect.php');
print("<br>");
print("<h1>Μαθήματα</h1>");
$id = $_SESSION['user_id'];

if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==3) {
    $display = 10; 
    $q = "SELECT COUNT(idcourses) FROM notes where idusers=?"; 
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'i', $id);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $count);
    my_mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($count > $display) { 
        $pages = ceil($count / $display); 
    } else {
        $pages = 1;
    }

    $positive_int_options = array('options' => array('min_range' => 0));
    if (!$start = filter_input(INPUT_GET, 'start', FILTER_VALIDATE_INT, $positive_int_options)) {
        $start = 0;    
    }

    if (!$sort = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING)) {
        $sort = 'avr';
    }

    switch ($sort) {
        case 'av':
            $order_by = 'n.average ASC';
            break;
        case 'tt':
            $order_by = 'c.title ASC';
            break;
        case 'ttr':
            $order_by = 'c.title DESC';
            break;
        case 'avr':
            $order_by = 'n.average DESC';
            break;    
        default:
            $order_by = 'n.average DESC';
            break;
    }

    $q = "SELECT c.title, c.idcourses, n.average FROM notes n "
        . "INNER JOIN courses c on c.idcourses=n.idcourses "
        . "WHERE n.idusers=$id "
        . "ORDER BY $order_by "
        . "LIMIT $start, $display ";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $title, $course, $average);

    print("<table>\n"); 
    print("<tr>\n"); 
    $link = ($sort == 'tt')? 'ttr': 'tt';
    print("<th><a href='notes.php?sort=$link'>Μάθημα</a></th>\n"); 
    $link = ($sort == 'avr')? 'av': 'avr';
    print("<th><a href='notes.php?sort=$link'>Μέσος Όρος</a></th>\n"); 
    print("<th>Ανάλυση Βαθμολογίας</th>\n"); 
    print("</tr>\n"); 

    while(mysqli_stmt_fetch($stmt)) { 
        print("<tr>\n");
        print("<td>$title</td>\n"); 
        print("<td>$average</td>\n");
        print("<td><a href='notes_details.php?id=$id&course=$course'>Λεπτομέρειες</a></td>\n");
        print("</tr>\n");
    }
    print("</table>\n");

    if ($pages > 1) {
        print("<p></p>\n");
        $current = ($start/$display) + 1; 
        if ($current != 1) { 
            $link = $start - $display;
            print("<a href='view_users.php?start=$link&sort=$sort'>Προηγούμενη </a>\n");
        }

        for ($i = 1; $i <= $pages; $i++) { 
            if ($i == $current) {           
                print("$i \n");
            } else {
                $link = ($i - 1) * $display;
                print("<a href='view_users.php?start=$link&sort=$sort'>$i </a>\n");
            }
        }

        if ($current != $pages) {
            $link = $start + $display;
            print("<a href='view_users.php?start=$link&sort=$sort'>Επόμενη </a>\n");
        }
    }
    include('includes/footer.php');
} else {
    print_access_error_exit();
}
?>