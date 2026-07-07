<?php 
 include "../includes/header.php";
 include "../includes/footer.php";
 include "../database/db.php";

 $sql="select count(*) as total_users from users";
 $result=mysqli_query($conn,$sql);
 $user=mysqli_fetch_assoc($result);
 $total_users=$user['total_users'];

  $sql="select count(*) as total_products from products";
 $result=mysqli_query($conn,$sql);
 $user=mysqli_fetch_assoc($result);
 $total_products=$user['total_products'];

  $sql="select count(*) as total_customers from customers";
 $result=mysqli_query($conn,$sql);
 $user=mysqli_fetch_assoc($result);
 $total_customers=$user['total_customers'];

  $sql="select count(*) as total_employees from employees";
 $result=mysqli_query($conn,$sql);
 $user=mysqli_fetch_assoc($result);
 $total_employees=$user['total_employees'];
?>
<div class="admin-container">
    <?php  include "../includes/admin_sidebar.php"; ?>
    <div>
    <h1>Welcome to Admin Dashboard</h1>
     <div class="cards">

        <div class="card">
            <h3>Total Users</h3>
            <p><?php echo $total_users; ?></p>
        </div>

        <div class="card">
            <h3>Total Products</h3>
            <p><?php echo $total_products; ?></p>
        </div>

        <div class="card">
            <h3>Total Customers</h3>
            <p><?php echo $total_customers; ?></p>
        </div>

        <div class="card">
            <h3>Total Employees</h3>
            <p><?php echo $total_employees; ?></p>
        </div>

</div>
</div>