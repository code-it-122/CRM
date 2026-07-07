<?php
include "../database/db.php";
if(isset($_GET['id'])){
     $id=$_GET['id'];
    if($id==1){
        echo "<script>alert('You cannot delete the admin user!');</script>";
        header("Location: view_user.php");
        exit();
    }
    elseif($id==$_SESSION['id']){
        echo "<script>alert('You cannot delete your own account!')</script>";
    }
    
    elseif($id > 1)
        {
    $sql = "DELETE FROM users WHERE user_id=?";
    $stmt=mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,"i",$id);
    mysqli_stmt_execute($stmt);
    header("Location: view_user.php");
    exit();
}
}
else{
    echo "Error in deleting user";
}
    
?>