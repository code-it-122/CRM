<?php
include "../database/db.php";
include "../includes/header.php";

// Auth Guard
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'sales'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Handle POST request from add_sales.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = intval($_POST['customer_id']);
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $sale_date = $_POST['sale_date'];

    if (empty($customer_id) || empty($product_id) || empty($quantity) || empty($sale_date)) {
        echo "<script>alert('Please fill all the required fields'); window.history.back();</script>";
        exit();
    }

    mysqli_begin_transaction($conn);
    $ok = true;
    $error_reason = '';

    try {
        // Step 1: verify enough stock exists and fetch price
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

        // Step 2: deduct stock (atomic guard)
        $sql = "UPDATE products SET stock = stock - ? WHERE product_id = ? AND stock >= ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $quantity, $product_id, $quantity);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) === 0) {
            throw new Exception('stock_conflict');
        }

        // Step 3: insert sale
        $created_by = $_SESSION['user_id'];
        $sql = "INSERT INTO sales (customer_id, sale_date, total_amount, created_by) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isdi", $customer_id, $sale_date, $total_amount, $created_by);
        mysqli_stmt_execute($stmt);
        $sale_id = mysqli_insert_id($conn);

        // Step 4: insert sale item
        $sql = "INSERT INTO sale_items (sale_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiidd", $sale_id, $product_id, $quantity, $price, $total_amount);
        mysqli_stmt_execute($stmt);

    } catch (Exception $e) {
        $ok = false;
        $error_reason = $e->getMessage();
    }

    if ($ok) {
        mysqli_commit($conn);
        echo "<script>
                alert('Sale added successfully');
                window.location.href = 'view_sales.php';
              </script>";
        exit();
    } else {
        mysqli_rollback($conn);
        $msg = ($error_reason === 'insufficient_stock')
            ? 'Not enough stock available for the selected product/quantity.'
            : 'Could not add the sale. Please try again.';
        echo "<script>alert('" . $msg . "'); window.history.back();</script>";
        exit();
    }
}

// Fetch Sales
$sql = "SELECT
            s.sale_id,
            c.name AS customer_name,
            s.sale_date,
            s.total_amount,
            u.name AS created_by
        FROM sales s
        JOIN customers c ON s.customer_id = c.customer_id
        LEFT JOIN users u ON s.created_by = u.user_id
        ORDER BY s.sale_id DESC";

$result = mysqli_query($conn, $sql);
$total_sales = mysqli_num_rows($result);
?>

<div class="admin-container">

<?php
if ($_SESSION['role'] == 'admin') {
    include "../includes/admin_sidebar.php";
} else {
    include "../includes/sales_sidebar.php";
}
?>

    <div class="view py-4 px-4">
        <?php
        $ph_icon = 'fa-money-bill-trend-up';
        $ph_title = 'Sales';
        $ph_subtitle = $total_sales . ' sale' . ($total_sales == 1 ? '' : 's') . ' on record';
        $ph_action_link = 'add_sales.php';
        $ph_action_label = 'Add Sale';
        $ph_action_icon = 'fa-plus';
        include "../includes/page_header.php";
        ?>

        <?php if ($total_sales > 0): ?>
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table-container">
                        <tr>
                            <th>Sale ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Created By</th>
                            <th colspan="2">Actions</th>
                        </tr>
                        <?php while($sale = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-2"
                                         style="width: 36px; height: 36px; flex-shrink: 0;">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <span class="fw-semibold text-dark">#<?php echo $sale['sale_id']; ?></span>
                                </div>
                            </td>
                            <td><span class="text-dark"><?php echo htmlspecialchars($sale['customer_name']); ?></span></td>
                            <td><span class="text-muted"><?php echo $sale['sale_date']; ?></span></td>
                            <td><span class="fw-bold text-dark">Rs <?php echo number_format($sale['total_amount'], 2); ?></span></td>
                            <td><span class="text-muted"><?php echo htmlspecialchars($sale['created_by']); ?></span></td>
                            <td class="edit-btn"><a href="edit_sales.php?id=<?php echo $sale['sale_id']; ?>"><i class="fa-solid fa-pen me-1"></i>Edit</a></td>
                            <td class="delete-btn">
                                <a href="delete_sales.php?id=<?php echo $sale['sale_id']; ?>"
                                   onclick="return confirm('Are you sure you want to delete this sale?');">
                                    <i class="fa-solid fa-trash me-1"></i>Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-3">
                <?php
                $es_icon = 'fa-money-bill-trend-up';
                $es_title = 'No sales yet';
                $es_desc = 'No sales have been recorded yet. Add your first sale to get started.';
                $es_action_link = 'add_sales.php';
                $es_action_label = 'Add Sale';
                include "../includes/empty_state.php";
                ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php include "../includes/footer.php"; ?>