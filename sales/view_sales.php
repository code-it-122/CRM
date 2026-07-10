<?php
 include "../database/db.php";

 if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $customer_id = $_POST['customer_id'];
    $product_id = $_POST['product_id'];
    $sale_date = $_POST['sale_date'];
    $created_by = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 1;
    $quantity = $_POST['quantity'];
    $price_query = "SELECT price FROM products WHERE product_id = ?";
    $price_stmt = mysqli_prepare($conn, $price_query);
    mysqli_stmt_bind_param($price_stmt, "i", $product_id);
    mysqli_stmt_execute($price_stmt);
    $price_result = mysqli_stmt_get_result($price_stmt);
    
    if ($product = mysqli_fetch_assoc($price_result)) {
        $price = $product['price'];
    } else {
        $price = 0; 
    }

    $total_amount = $price * $quantity;


    $sql = "INSERT INTO sales (customer_id, sale_date, total_amount, created_by) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isdi", $customer_id, $sale_date, $total_amount, $created_by);
    $result = mysqli_stmt_execute($stmt);

    if($result){
        $sale_id = mysqli_insert_id($conn);
        $subtotal = $price * $quantity;

        // Insert into sale_items
        $item_sql = "INSERT INTO sale_items (sale_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)";
        $item_stmt = mysqli_prepare($conn, $item_sql);
        mysqli_stmt_bind_param($item_stmt, "iiidd", $sale_id, $product_id, $quantity, $price, $subtotal);
        mysqli_stmt_execute($item_stmt);

        // Update product stock
        $stock_sql = "UPDATE products SET stock = stock - ? WHERE product_id = ?";
        $stock_stmt = mysqli_prepare($conn, $stock_sql);
        mysqli_stmt_bind_param($stock_stmt, "ii", $quantity, $product_id);
        mysqli_stmt_execute($stock_stmt);

        echo "<script>
                alert('Sale added successfully');
                window.location.href = 'view_sales.php';
              </script>";
        exit();
    } else {
        die("Error: " . mysqli_stmt_error($stmt));
    }
 }

 include "../includes/header.php";
?>

<div class='admin-container'>
 <?php if($_SESSION['role'] == 'admin'){
    include "../includes/admin_sidebar.php";
}
elseif($_SESSION['role'] == 'sales'){
    include "../includes/sales_sidebar.php";
} ?>
 <div class="view">
     <?php
     $sql = "SELECT s.sale_id, c.name, s.sale_date, s.total_amount, s.created_by 
             FROM sales s 
             JOIN customers c ON s.customer_id = c.customer_id";
     $result = mysqli_query($conn, $sql);
     ?>
     
     <h1>Sales</h1>
     <hr>
     <a href='add_sales.php' class='add-btn'>Add Sales</a>
     <table class="table-container">
         <tr>
             <th>Sales ID</th>
             <th>Customer</th>
             <th>Sales Date</th>
             <th>Total Amount</th>
             <th>Created By</th>
             <th colspan='2'>Actions</th>
         </tr>
         <?php
         while($sale = mysqli_fetch_assoc($result)){
             echo "<tr>";
             echo "<td>" . $sale['sale_id'] . "</td>";
             echo "<td>" . $sale['name'] . "</td>";
             echo "<td>" . $sale['sale_date'] . "</td>";
             echo "<td>" . $sale['total_amount'] . "</td>";
             echo "<td>" . $sale['created_by'] . "</td>";
             echo "<td class='edit-btn'><a href='edit_sales.php?id=" . $sale['sale_id'] . "'>Edit</a></td>";
             echo "<td class='delete-btn'>
                     <a href='delete_sales.php?id=" . $sale['sale_id'] . "' 
                        onclick=\"return confirm('Are you sure you want to delete this sale?');\">
                        Delete
                     </a>
                   </td>";
             echo "</tr>";
         }
         ?>
     </table>
 </div>
</div>

<?php include "../includes/footer.php"; ?>