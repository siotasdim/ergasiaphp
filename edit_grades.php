<?php
session_start();
require_once('includes/helper_functions.php');
require_once('mysqli_connect.php');
$page_title = 'Επεξεργασία Βαθμολογίας';
include('includes/header.php');
print("<br>");
print("<h1>Επεξεργασία Βαθμολογίας</h1>");

$id = $_SESSION['user_id'];
if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==2) {

    if (!$studentid = filter_input(INPUT_GET, 'studentid', FILTER_VALIDATE_INT)) {
        if (!$studentid = filter_input(INPUT_POST, 'studentid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }

    if (!$courseid = filter_input(INPUT_GET, 'courseid', FILTER_VALIDATE_INT)) {
        if (!$courseid = filter_input(INPUT_POST, 'courseid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }

    if (!$classid = filter_input(INPUT_GET, 'classid', FILTER_VALIDATE_INT)) {
        if (!$classid = filter_input(INPUT_POST, 'classid', FILTER_VALIDATE_INT)) {
                print_access_error_exit();
        }
    }

    $nogradeinnotes = false;

    if (filter_input(INPUT_POST, 'submit')) {
        $errors = array();
        if (filter_input(INPUT_POST, "oralnote")!=null) { 
            $oralnote=(filter_input(INPUT_POST, "oralnote"));
            if (filter_var($oralnote, FILTER_VALIDATE_INT) === 0 || filter_var($oralnote, FILTER_VALIDATE_INT)) {
            } else {
                $errors[] = 'Ο βαθμός προφορικής εξέτασης που καταχωρήσατε είναι εσφαλμένος';
            }
        } else {
            $errors[] = 'Ξεχάσατε να δηλώσετε βαθμό προφορικής εξέτασης';
        }
        if (filter_input(INPUT_POST, "testnote")!=null) { 
            $testnote=(filter_input(INPUT_POST, "testnote"));
            if (filter_var($testnote, FILTER_VALIDATE_INT) === 0 || filter_var($testnote, FILTER_VALIDATE_INT)) {
            } else {
                $errors[] = 'Ο βαθμός γραπτής εξέτασης που καταχωρήσατε είναι εσφαλμένος';
            }
        } else {
            $errors[] = 'Ξεχάσατε να δηλώσετε βαθμό γραπτής εξέτασης';
        }
        if (!empty($errors)) {
            print_error_message($errors);
        } else {  
            $q = "SELECT idusers FROM notes where idusers=? and idcourses=?";
            $stmt = my_mysqli_prepare($dbc, $q);
            my_mysqli_stmt_bind_param($stmt, 'ii', $studentid, $courseid);
            my_mysqli_stmt_execute($stmt);
            my_mysqli_stmt_store_result($stmt);
            if (my_mysqli_stmt_num_rows($stmt) > 0) {  //αμα δηλαδη einai sto pinaka to pair mathitis-mathima
                $nogradeinnotes = true;
            }
            mysqli_stmt_free_result($stmt);
            mysqli_stmt_close($stmt);

            if ($nogradeinnotes==false) { /////////////////////////////////////////////////////////////////
                mysqli_close($dbc); 
                include('includes/footer.php');
                exit(); //////////////////////////////////////////////////
            } else {
                $q = "UPDATE notes SET oralnote = ?, testnote=? WHERE idusers = ? and idcourses=?";
                $stmt = my_mysqli_prepare($dbc, $q);
                my_mysqli_stmt_bind_param($stmt, 'iiii', $oralnote, $testnote, $studentid, $courseid);
                my_mysqli_stmt_execute($stmt);
                if (my_mysqli_stmt_affected_rows($stmt) == 1) {
                    print("<p>Έχετε καταχωρήσει επιτυχώς την βαθμολογία!</p>\n");
                } else {
                    print("<p>Η βαθμολογία δεν καταχωρήθηκε.</p>\n");
                }
                mysqli_stmt_free_result($stmt);
                mysqli_stmt_close($stmt); 
                mysqli_close($dbc); 
                include('includes/footer.php');
                exit(); 
            } 
        }
    }

    $q = "SELECT l.title, c.title, u.idusers, u.lastname, u.firstname, n.oralnote, n.testnote, n.average
                            from notes n inner join courses c on c.idcourses=n.idcourses
                            inner join studentsclasses cs on cs.idusers=n.idusers
                            inner join classes l on l.idclasses=cs.idclasses
                            inner join users u on u.idusers=n.idusers
                            where l.idclasses = ? and n.idcourses = ? and u.idusers = ? ";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'iii', $classid, $courseid, $studentid);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 1) { //dld an iparxei o mathitis ston pinaka notes
        my_mysqli_stmt_bind_result($stmt, $classtitle, $coursetitle, $studentid, $lastname, $firstname, $oralnote, $testnote, $average);
        mysqli_stmt_fetch($stmt); 
?>
        <form action="" method="post">
            <p>Όνομα: <input type="text" name="firstname" value="<?php print($firstname); ?>" readonly="readonly"></p>
            <p>Επώνυμο: <input type="text" name="lastname" value="<?php print($lastname); ?>" readonly="readonly"></p>
            <p>Σειρά ΣΠΗΥ: <input type="text" name="classtitle" value="<?php print($classtitle); ?>" readonly="readonly"></p>
            <p>Μάθημα: <input type="text" name="coursetitle" value="<?php print($coursetitle); ?>" readonly="readonly"></p>
            <p>Προφορικός Βαθμός: <input type="number" name="oralnote" min='0' max='100' step='1' value="<?php print($oralnote); ?>"></p>
            <p>Γραπτή Εξέταση: <input type="number" name="testnote" min='0' max='100' step='1'  value="<?php print($testnote); ?>"></p>
            <p>Μέσος Όρος: <input type="text" name="average" value="<?php print($average); ?>" readonly="readonly"></p>
            <p><input type="submit" name="submit" value="Καταχώρηση Βαθμολογίας"></p>
            <input type="hidden" name="studentid" value="<?php print($studentid); ?>">
            <input type="hidden" name="courseid" value="<?php print($courseid); ?>">
            <input type="hidden" name="classid" value="<?php print($classid); ?>">
        </form>
<?php
        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);
        include('includes/footer.php');
        exit();
    } else { //καταχωρηση στον πινακα notes
        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);
        $q = "INSERT INTO notes (idusers, idcourses) VALUES (?, ?)";
        $stmt = my_mysqli_prepare($dbc, $q);
        my_mysqli_stmt_bind_param($stmt, 'ii', $studentid, $courseid);
        my_mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt); 

        $q = "SELECT l.title, c.title, u.idusers, u.lastname, u.firstname, n.oralnote, n.testnote, n.average
            from notes n inner join courses c on c.idcourses=n.idcourses
            inner join studentsclasses cs on cs.idusers=n.idusers
            inner join classes l on l.idclasses=cs.idclasses
            inner join users u on u.idusers=n.idusers
            where l.idclasses = ? and n.idcourses = ? and u.idusers = ? ";
        $stmt = my_mysqli_prepare($dbc, $q);
        my_mysqli_stmt_bind_param($stmt, 'iii', $classid, $courseid, $studentid);
        my_mysqli_stmt_execute($stmt);
        my_mysqli_stmt_store_result($stmt);
        my_mysqli_stmt_bind_result($stmt, $classtitle, $coursetitle, $studentid, $lastname, $firstname, $oralnote, $testnote, $average);
        mysqli_stmt_fetch($stmt); 
?>
        <form action="" method="post">
            <p>Όνομα: <input type="text" name="firstname" value="<?php print($firstname); ?>" readonly="readonly"></p>
            <p>Επώνυμο: <input type="text" name="lastname" value="<?php print($lastname); ?>" readonly="readonly"></p>
            <p>Σειρά ΣΠΗΥ: <input type="text" name="classtitle" value="<?php print($classtitle); ?>" readonly="readonly"></p>
            <p>Μάθημα: <input type="text" name="coursetitle" value="<?php print($coursetitle); ?>" readonly="readonly"></p>
            <p>Προφορικός Βαθμός: <input type="number" name="oralnote" min='0' max='100' step='1' value="<?php print($oralnote); ?>"></p>
            <p>Γραπτή Εξέταση: <input type="number" name="testnote" min='0' max='100' step='1'  value="<?php print($testnote); ?>"></p>
            <p>Μέσος Όρος: <input type="text" name="average" value="<?php print($average); ?>" readonly="readonly"></p>
            <p><input type="submit" name="submit" value="Καταχώρηση Βαθμολογίας"></p>
            <input type="hidden" name="studentid" value="<?php print($studentid); ?>">
            <input type="hidden" name="courseid" value="<?php print($courseid); ?>">
            <input type="hidden" name="classid" value="<?php print($classid); ?>">
        </form>
<?php        
        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($dbc);
        include('includes/footer.php');
        exit();
    }
} else {
    print_access_error_exit();
}
?>