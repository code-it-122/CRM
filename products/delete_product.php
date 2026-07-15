<?php
include "../database/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if(isset($_GET['id'])){
     $id=$_GET['id'];

    if($id > 0)
        {
    $sql = "DELETE FROM products WHERE product_id=?";
    $stmt=mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,"i",$id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view_product.php");
        exit();
    } else {
        echo "<script>alert('This product cannot be deleted because it has existing sales records.'); window.location.href='view_product.php';</script>";
        exit();
    }
}
}
else{
    echo "Error in deleting product";
}
    
?>