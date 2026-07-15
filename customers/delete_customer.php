<?php
include "../database/db.php";

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'sales'])) {
    header("Location: ../auth/login.php");
    exit();
}

if(isset($_GET['id'])){
     $id=$_GET['id'];

    if($id > 0)
        {
    $sql = "DELETE FROM customers WHERE customer_id=?";
    $stmt=mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,"i",$id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view_customer.php");
        exit();
    } else {
        echo "<script>alert('This customer cannot be deleted because they have existing sales records.'); window.location.href='view_customer.php';</script>";
        exit();
    }
}
}
else{
    echo "Error in deleting customer";
}
    
?>