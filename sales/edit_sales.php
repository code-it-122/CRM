<?php
include "../database/db.php";
include "../includes/header.php";

// 1. Handle Form Submission (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sale_id = $_POST['sale_id'];
$customer_id = $_POST['customer_id'];
$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];
$sale_date = $_POST['sale_date'];

// Get latest product price
$sql = "SELECT price FROM products WHERE product_id=?";
$stmt = mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($stmt,"i",$product_id);
mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);
$product=mysqli_fetch_assoc($result);

$price=$product['price'];
$total_amount=$price*$quantity;

// Update sales
$sql="UPDATE sales
SET customer_id=?,
sale_date=?,
total_amount=?
WHERE sale_id=?";

$stmt=mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
$stmt,
"isdi",
$customer_id,
$sale_date,
$total_amount,
$sale_id
);

mysqli_stmt_execute($stmt);

// Update sale_items
$subtotal=$price*$quantity;

$sql="UPDATE sale_items
SET product_id=?,
quantity=?,
price=?,
subtotal=?
WHERE sale_id=?";

$stmt=mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
$stmt,
"iiddi",
$product_id,
$quantity,
$price,
$subtotal,
$sale_id
);

mysqli_stmt_execute($stmt);

// Also update invoices total_amount if an invoice exists for this sale
$sql_invoice = "UPDATE invoices SET total_amount = ? WHERE sale_id = ?";
$stmt_invoice = mysqli_prepare($conn, $sql_invoice);
mysqli_stmt_bind_param($stmt_invoice, "di", $total_amount, $sale_id);
mysqli_stmt_execute($stmt_invoice);

header("Location:view_sales.php");
exit();
}

if(isset($_GET['id'])){

    $id = $_GET['id'];

    // Fetch sale
    $sql = "SELECT * FROM sales WHERE sale_id=?";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,"i",$id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $sale = mysqli_fetch_assoc($result);

    // Fetch sale item
    $sql = "SELECT * FROM sale_items WHERE sale_id=?";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,"i",$id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $sale_item = mysqli_fetch_assoc($result);

    // Customers
    $customers_result = mysqli_query($conn,"SELECT * FROM customers");

    // Products
    $products_result = mysqli_query($conn,"SELECT * FROM products");
}
?>

<div class="admin-container">
    <?php include "../includes/admin_sidebar.php"; ?>
    <div class="add-user">
        <h1>Edit Sale</h1>
        <form action="edit_sales.php" method="POST">
            <!-- Hidden Sale ID -->
            <input type="hidden" name="sale_id" value="<?php echo $sale['sale_id']; ?>">

            <!-- Customer -->
            <label for="customer_id">Customer:</label>
            <select name="customer_id" id="customer_id" required>
                <option value="">Select Customer</option>
                <?php
                while ($cust = mysqli_fetch_assoc($customers_result)) {
                    $selected = ($cust['customer_id'] == $sale['customer_id']) ? "selected" : "";
                    echo "<option value='" . $cust['customer_id'] . "' $selected>" . $cust['name'] . "</option>";
                }
                ?>
            </select><br>

            <label for="product_id">Product:</label>
<select name="product_id" required>

<option value="">Select Product</option>

<?php
while($prod=mysqli_fetch_assoc($products_result)){
    $selected = ($prod['product_id']==$sale_item['product_id']) ? "selected" : "";
    echo "<option value='".$prod['product_id']."' ".$selected.">".$prod['product_name']."</option>";
}
?>

</select><br>
<label for="quantity">Quantity:</label>
<input
type="number"
name="quantity"
value="<?php echo $sale_item['quantity']; ?>"
required><br>
            <!-- Sale Date -->
            <label for="sale_date">Sale Date:</label>
            <input type="date" id="sale_date" name="sale_date" value="<?php echo $sale['sale_date']; ?>" required><br>

           
            <button type="submit">Edit Sale</button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>