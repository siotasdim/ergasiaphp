<!DOCTYPE html>
<html>
<head>
    <title><?php print($page_title);?></title>
    <meta charset="utf8">
    <link rel="stylesheet" href="includes/style.css">
</head>
<body>
    <div id="header">
        <h1>Σ.Π.Η.Υ.</h1>
        <h2>Το μέλλον στην εκπαίδευση</h2>
    </div>
    <div id="navigation">
        <ul>
            <li><a href="index.php">Αρχική Σελίδα</a></li>
            <?php   
            if (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==2) {
                //Καθηγητές
                print("<li><a href='courses.php'>Διδασκαλία</a></li>");
                print("<li><a href='prof_view_notes.php'>Βαθμολογίες</a></li>");
                print("<li><a href='change_user_info.php'>Αλλαγή Στοιχείων Χρήστη</a></li>");
            } elseif (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==3) {
                //Μαθητές
                print("<li><a href='notes.php'>Μαθήματα</a></li>");
                print("<li><a href='change_user_info.php'>Αλλαγή Στοιχείων Χρήστη</a></li>");
            } elseif (isset($_SESSION['user_id']) && ($_SESSION['role_id'])==1) {
                //Γραφείο Εκπαίδευσης
                print("<li><a href='view_classes.php'>Σειρές</a></li>");
                print("<li><a href='view_courses.php'>Μαθήματα</a></li>");
                print("<li><a href='view_profs.php'>Καθηγητές</a></li>");
                print("<li><a href='view_students.php'>Μαθητές</a></li>");
                print("<li><a href='allnotes.php'>Βαθμολογίες Μαθητών</a></li>");
                print("<li><a href='change_user_info.php'>Αλλαγή Στοιχείων Χρήστη</a></li>");
                print("<li><a href='register_user.php'>Εγγραφή Νέου Χρήστη</a></li>");
            } 
            ?>
            <li><?php
            if (isset($_SESSION['user_id']) && (!strpos($_SERVER['PHP_SELF'], 'logout.php'))) {
                print("<a href='logout.php'>Logout</a>");
            } else {
                print("<a href='login.php'>Login</a>");
            }
            ?></li>
        </ul>
    </div>
    <div id="content">