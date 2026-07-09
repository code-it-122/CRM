<?php
include "../includes/header.php";
include "../database/db.php";

$sql = "SELECT * FROM customers";
$result = mysqli_query($conn, $sql);

$sql = "SELECT * FROM products";
$result1 = mysqli_query($conn, $sql);
?>

<div class="admin-container">
    <?php include "../includes/admin_sidebar.php"; ?>

    <div class="add-user">
        <h1>Add Sale</h1>
        <form action="view_sales.php" method="POST">
            <label>Customer</label>
            <select name="customer_id" required>
                <option value="">Select Customer</option>
                <?php
                while($cust = mysqli_fetch_assoc($result)){
                    echo "<option value='".$cust['customer_id']."'>".$cust['name']."</option>";
                }
                ?>
            </select>

            <label>Product</label>
            <select name="product_id" required>
                <option value="">Select Product</option>
                <?php
                while($prod = mysqli_fetch_assoc($result1)){
                    echo "<option value='".$prod['product_id']."'>".$prod['product_name']."</option>";
                }
                ?>
            </select>

            <label>Quantity</label>
            <input type="number" name="quantity" min="1" required>

            <label>Sale Date</label>
            <input type="date" name="sale_date" required>

            <button type="submit">Add Sale</button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>