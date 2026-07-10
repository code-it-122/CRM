<?php 
 include "../includes/header.php";
 include "../database/db.php";

  $sql="select count(*) as total_products from products";
 $result=mysqli_query($conn,$sql);
 $user=mysqli_fetch_assoc($result);
 $total_products=$user['total_products'];

  $sql="select count(*) as total_customers from customers";
 $result=mysqli_query($conn,$sql);
 $user=mysqli_fetch_assoc($result);
 $total_customers=$user['total_customers'];

   $sql="select count(*) as total_sales from sales";
 $result=mysqli_query($conn,$sql);
 $sales=mysqli_fetch_assoc($result);
 $total_sales=$sales['total_sales'];

?>
<div class="admin-container">
    <?php  if ($_SESSION['role'] == 'admin') {
        include "../includes/admin_sidebar.php";
    } 
    elseif ($_SESSION['role'] == 'sales') {
        include "../includes/sales_sidebar.php";
    }
     ?>
    <div class="view">
    <h1>Welcome to Sales Dashboard</h1>
     <div class="cards">

        <div class="card">
            <h3>Total Products</h3>
            <p><?php echo $total_products; ?></p>
        </div>

        <div class="card">
            <h3>Total Customers</h3>
            <p><?php echo $total_customers; ?></p>
        </div>

        <div class="card">
            <h3>Total Sales</h3>
            <p><?php echo $total_sales; ?></p>
        </div>
