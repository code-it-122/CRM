<?php
 include "../includes/header.php";
 include "../includes/footer.php";
 include "../database/db.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $sql="insert into customers (name,phone,email,address) values(?,?,?,?)";
    $stmt=mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt, "ssss", $name, $phone, $email, $address);
    $result=mysqli_stmt_execute($stmt);

    if($result){
        echo "<script>alert('Customer added successfully');</script>";
        header("Location: view_customer.php");
        exit();
    } else {
        die("Error: " . mysqli_stmt_error($stmt));
    }

 }


 echo "<div class='admin-container'>";
 include "../includes/admin_sidebar.php";
 echo "<div class=\"view\">";
 $sql="SELECT * FROM customers";
 $result=mysqli_query($conn,$sql);

 
 echo "<h1>Customers</h1><hr>";
 echo "<a href='add_customer.php' class='add-btn'>Add Customer</a>";
 echo "<table class=\"table-container\">";
echo "<tr>
        <th>Customer ID</th>
        <th>Customer Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Address</th>
        <th colspan='2'>Actions</th>
      </tr>";
      while( $Customer=mysqli_fetch_assoc($result)){
 echo "<tr><td>".$Customer['customer_id']."</td><td>".
       $Customer['name']."</td><td>".
       $Customer['phone']."</td><td>".
       $Customer['email']."</td><td>".
       $Customer['address']."</td>";
       echo "<td class=\"edit-btn\"><a href='edit_customer.php?id=".$Customer['customer_id']."'>Edit</a></td>";
 echo "<td class=\"delete-btn\">
        <a href='delete_customer.php?id=".$Customer['customer_id']."'
           onclick=\"return confirm('Are you sure you want to delete this user?');\">
           Delete
        </a>
      </td></tr>";
          echo "</tr>";
      }

 echo "</table>";
 echo "</div>";
 echo "</div>";
?>