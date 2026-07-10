<?php
include "../includes/header.php";
?>

<div class="admin-container">
    <?php 
    if ($_SESSION['role'] == 'admin') {
        include "../includes/admin_sidebar.php";
    } 
    elseif ($_SESSION['role'] == 'sales') {
        include "../includes/sales_sidebar.php";
    }
    elseif ($_SESSION['role'] == 'hr') {
        include "../includes/hr_sidebar.php";
    }
    ?>
    <div class="view">
       <h1 style="Text-align:center">This page is Still under Construction</h1>
        <hr>
    
    </div>
</div>

<?php include "../includes/footer.php"; ?>