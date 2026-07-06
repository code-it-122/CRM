<?php
 include "../includes/header.php";
 include "../includes/footer.php";
 include "../database/db.php";
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
      </tr>";
      while( $Customer=mysqli_fetch_assoc($result)){
 echo "<tr><td>".$Customer['customer_id']."</td><td>".
       $Customer['customer_name']."</td><td>".
       $Customer['phone']."</td><td>".
       $Customer['email']."</td><td>".
       $Customer['address']."</td>";
          echo "</tr>";
      }

 echo "</table>";
 echo "</div>";
 echo "</div>";
?>