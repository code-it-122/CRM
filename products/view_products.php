<?php
 include "../includes/header.php";
 include "../includes/footer.php";
 include "../database/db.php";
 echo "<div class='admin-container'>";
 include "../includes/admin_sidebar.php";
 echo "<div class=\"view\">";
 $sql="SELECT * FROM products";
 $result=mysqli_query($conn,$sql);

 
 echo "<h1>Products</h1><hr>";
 echo "<a href='add_product.php' class='add-btn'>Add Product</a>";
 echo "<table class=\"table-container\">";
echo "<tr>
        <th>Product ID</th>
        <th>Product Name</th>
        <th>Category</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Description</th>
        <th colspan='2'>Actions</th>
      </tr>";
      while( $Product=mysqli_fetch_assoc($result)){
 echo "<tr><td>".$Product['product_id']."</td><td>".
       $Product['product_name']."</td><td>".
       $Product['category']."</td><td>".
       $Product['price']."</td><td>".
       $Product['stock']."</td><td>".
       $Product['description']."</td>";
          echo "</tr>";
          echo "<td class=\"edit-btn\"><a href='edit_user.php?id=".$products['product_id']."'>Edit</a></td>";
 echo "<td class=\"delete-btn\">
        <a href='delete_user.php?id=".$products['product_id']."'
           onclick=\"return confirm('Are you sure you want to delete this user?');\">
           Delete
        </a>
      </td></tr>";
      }

 echo "</table>";
 echo "</div>";
 echo "</div>";
?>