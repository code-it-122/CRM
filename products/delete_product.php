<?php
include "../database/db.php";
if(isset($_GET['id'])){
     $id=$_GET['id'];

    if($id > 0)
        {
    $sql = "DELETE FROM products WHERE product_id=?";
    $stmt=mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,"i",$id);
    mysqli_stmt_execute($stmt);
    header("Location: view_product.php");
    exit();
}
}
else{
    echo "Error in deleting product";
}
    
?>