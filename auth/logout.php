include 
<?php
 session_start();

 if(isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
    unset($_SESSION['name']);
    unset($_SESSION['role']);
     session_destroy();
    header("location: ../auth/login.php");
    exit();
 }


?>