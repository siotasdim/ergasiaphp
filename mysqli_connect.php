<?php 

$db_user = 'root';
$db_password = '';
$db_host = 'localhost';
$db_name = 'ergasiaphp';

$dbc = mysqli_connect($db_host, $db_user, $db_password, $db_name)
        OR die('Δεν ειναι δυνατή η σύνδεση στη MySQL: ' . mysqli_connect_error());
//αν δουλέψει σωστά, ο browser θα βγάλει λευκή σελίδα 
//αλλιώς θα βγάλει το μήνυμα

//η τελεία . κάνει συνένωση (concatenation) των δύο μεταβλητών



//delete from users
//where user_id=xxxxx
?>