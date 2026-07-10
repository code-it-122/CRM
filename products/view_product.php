<?php
 include "../includes/header.php";
 include "../database/db.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    $sql="insert into products (product_name,category,price,stock,description) values(?,?,?,?,?)";
    $stmt=mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt, "ssdis", $product_name, $category, $price, $stock, $description);
    $result=mysqli_stmt_execute($stmt);

    if($result){
        echo "<script>alert('Product added successfully');</script>";
        header("Location: view_product.php");
        exit();
    } else {
        die("Error: " . mysqli_stmt_error($stmt));
    }

 }



echo "<div class='admin-container'>";
if($_SESSION['role'] == 'admin'){
    include "../includes/admin_sidebar.php";
}
elseif($_SESSION['role'] == 'sales'){
    include "../includes/sales_sidebar.php";
}
elseif($_SESSION['role'] == 'hr'){
    include "../includes/hr_sidebar.php";
}

 echo "<div class=\"view\">";
 $sql="SELECT * FROM products";
 $result=mysqli_query($conn,$sql);

 
 echo "<h1>Products</h1><hr>";

 if ($_SESSION['role'] == 'admin') {
    echo "<a href='add_product.php' class='add-btn'>Add Product</a>";
}
 echo "<table class=\"table-container\">";
echo "<tr>
        <th>Product ID</th>
        <th>Product Name</th>
        <th>Category</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Description</th>";

       if($_SESSION['role'] == 'admin'){
    echo "<th colspan='2'>Actions</th>";
}
      echo "</tr>";
      while( $Product=mysqli_fetch_assoc($result)){
 echo "<tr><td>".$Product['product_id']."</td><td>".
       $Product['product_name']."</td><td>".
       $Product['category']."</td><td>".
       $Product['price']."</td><td>".
       $Product['stock']."</td><td>".
       $Product['description']."</td>";
       if($_SESSION['role'] == 'admin'){
          echo "<td class=\"edit-btn\"><a href='edit_product.php?id=".$Product['product_id']."'>Edit</a></td>";
 echo "<td class=\"delete-btn\">
        <a href='delete_product.php?id=".$Product['product_id']."'
           onclick=\"return confirm('Are you sure you want to delete this user?');\">
           Delete
        </a>
      </td></tr>";
      }}

 echo "</table>";
 echo "</div>";
 echo "</div>";
?>