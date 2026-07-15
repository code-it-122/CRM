<?php
ob_start();
include "../database/db.php";
include "../includes/header.php";

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'sales'])) {
    header("Location: ../auth/login.php");
    exit();
}

// 1. Handle Form Submission (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sale_id = intval($_POST['sale_id']);
    $customer_id = intval($_POST['customer_id']);
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $sale_date = $_POST['sale_date'];

    // Fetch the OLD sale item so we know what stock to restore
    $sql = "SELECT product_id, quantity FROM sale_items WHERE sale_id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $sale_id);
    mysqli_stmt_execute($stmt);
    $old_item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$old_item) {
        echo "<script>alert('Original sale record not found.'); window.history.back();</script>";
        exit();
    }

    $old_product_id = $old_item['product_id'];
    $old_quantity = $old_item['quantity'];

    mysqli_begin_transaction($conn);
    $ok = true;

    try {
        // Step 1: restore stock to the OLD product
        $sql = "UPDATE products SET stock = stock + ? WHERE product_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $old_quantity, $old_product_id);
        mysqli_stmt_execute($stmt);

        // Step 2: verify enough stock exists on the NEW product (after restoring, in case it's the same product)
        $sql = "SELECT price, stock FROM products WHERE product_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$product || $quantity > $product['stock']) {
            throw new Exception('insufficient_stock');
        }

        $price = $product['price'];
        $total_amount = $price * $quantity;

        // Step 3: deduct new quantity from the NEW product (atomic guard)
        $sql = "UPDATE products SET stock = stock - ? WHERE product_id = ? AND stock >= ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $quantity, $product_id, $quantity);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) === 0) {
            throw new Exception('stock_conflict');
        }

        // Step 4: update sales
        $sql = "UPDATE sales SET customer_id=?, sale_date=?, total_amount=? WHERE sale_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isdi", $customer_id, $sale_date, $total_amount, $sale_id);
        mysqli_stmt_execute($stmt);

        // Step 5: update sale_items
        $sql = "UPDATE sale_items SET product_id=?, quantity=?, price=?, subtotal=? WHERE sale_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiddi", $product_id, $quantity, $price, $total_amount, $sale_id);
        mysqli_stmt_execute($stmt);

        // Step 6: keep invoice total in sync if one exists
        $sql = "UPDATE invoices SET total_amount = ? WHERE sale_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "di", $total_amount, $sale_id);
        mysqli_stmt_execute($stmt);

    } catch (Exception $e) {
        $ok = false;
        $error_reason = $e->getMessage();
    }

    if ($ok) {
        mysqli_commit($conn);
        header("Location: view_sales.php");
        exit();
    } else {
        mysqli_rollback($conn);
        $msg = ($error_reason === 'insufficient_stock')
            ? 'Not enough stock available for the selected product/quantity.'
            : 'Could not update the sale. Please try again.';
        echo "<script>alert('" . $msg . "'); window.history.back();</script>";
        exit();
    }
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

    if (!$sale) {
        echo "<script>alert('Sale not found.'); window.location.href='view_sales.php';</script>";
        exit();
    }

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
} else {
    header("Location: view_sales.php");
    exit();
}
?>

<div class="admin-container">
    <?php if($_SESSION['role'] == 'admin'){
        include "../includes/admin_sidebar.php";
    } elseif($_SESSION['role'] == 'sales'){
        include "../includes/sales_sidebar.php";
    } ?>

    <div class="view py-4 px-4">
        <?php
        $ph_icon = 'fa-pen-to-square';
        $ph_title = 'Edit Sale';
        $ph_subtitle = 'Update sale details.';
        $ph_back_link = 'view_sales.php';
        $ph_back_label = 'Back to Sales';
        include "../includes/page_header.php";
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="edit_sales.php" method="POST">
                            <input type="hidden" name="sale_id" value="<?php echo $sale['sale_id']; ?>">

                            <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-user me-1"></i> Customer &amp; Product
                            </h6>
                            <div class="mb-3">
                                <label for="customer_id" class="form-label fw-semibold text-dark">Customer</label>
                                <select name="customer_id" id="customer_id" class="form-select" required>
                                    <option value="">Select Customer</option>
                                    <?php
                                    while ($cust = mysqli_fetch_assoc($customers_result)) {
                                        $selected = ($cust['customer_id'] == $sale['customer_id']) ? "selected" : "";
                                        echo "<option value='" . $cust['customer_id'] . "' $selected>" . htmlspecialchars($cust['name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="product_id" class="form-label fw-semibold text-dark">Product</label>
                                <select name="product_id" id="product_id" class="form-select" required>
                                    <option value="">Select Product</option>
                                    <?php
                                    while($prod=mysqli_fetch_assoc($products_result)){
                                        $selected = ($prod['product_id']==$sale_item['product_id']) ? "selected" : "";
                                        // Show effective stock: current stock + what THIS sale already holds (since it will be restored on save)
                                        $effective_stock = $prod['stock'] + ($prod['product_id'] == $sale_item['product_id'] ? $sale_item['quantity'] : 0);
                                        echo "<option value='".$prod['product_id']."' ".$selected.">".htmlspecialchars($prod['product_name'])." (Available: ".$effective_stock.")</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-cart-shopping me-1"></i> Sale Details
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="quantity" class="form-label fw-semibold text-dark">Quantity</label>
                                    <input
                                    type="number"
                                    name="quantity"
                                    id="quantity"
                                    class="form-control"
                                    min="1"
                                    value="<?php echo $sale_item['quantity']; ?>"
                                    required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="sale_date" class="form-label fw-semibold text-dark">Sale Date</label>
                                    <input type="date" id="sale_date" name="sale_date" class="form-control" value="<?php echo $sale['sale_date']; ?>" required>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill py-2 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-2"></i>Save Changes
                                </button>
                                <a href="view_sales.php" class="btn btn-outline-secondary py-2 px-4">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>