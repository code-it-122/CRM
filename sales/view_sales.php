<?php
include "../includes/header.php";
include "../database/db.php";

// Auth guard
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'sales'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Handle sale submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['customer_id'])) {
    $customer_id = intval($_POST['customer_id']);
    $product_id  = intval($_POST['product_id']);
    $quantity    = intval($_POST['quantity']);
    $sale_date   = $_POST['sale_date'];

    if ($customer_id <= 0 || $product_id <= 0 || $quantity < 1 || empty($sale_date)) {
        echo "<script>alert('Please fill all the required fields correctly.');</script>";
    } else {
        // Lock-read the product to check live stock/price
        $sql = "SELECT price, stock FROM products WHERE product_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$product) {
            echo "<script>alert('Selected product no longer exists.');</script>";
        } elseif ($quantity > $product['stock']) {
            echo "<script>alert('Cannot sell more than available stock. Only " . $product['stock'] . " left.');</script>";
        } else {
            $price = $product['price'];
            $total_amount = $price * $quantity;
            $created_by = $_SESSION['user_id'];

            mysqli_begin_transaction($conn);
            $ok = true;

            try {
                $sql = "INSERT INTO sales (customer_id, sale_date, total_amount, created_by) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "isdi", $customer_id, $sale_date, $total_amount, $created_by);
                mysqli_stmt_execute($stmt);
                $sale_id = mysqli_insert_id($conn);

                $sql = "INSERT INTO sale_items (sale_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iiidd", $sale_id, $product_id, $quantity, $price, $total_amount);
                mysqli_stmt_execute($stmt);

                // Atomic stock deduction — the "AND stock >= ?" guards against a race condition
                $sql = "UPDATE products SET stock = stock - ? WHERE product_id = ? AND stock >= ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iii", $quantity, $product_id, $quantity);
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) === 0) {
                    throw new Exception('stock_conflict');
                }
            } catch (Exception $e) {
                $ok = false;
            }

            if ($ok) {
                mysqli_commit($conn);
                echo "<script>alert('Sale recorded successfully'); window.location.href='view_sales.php';</script>";
                exit();
            } else {
                mysqli_rollback($conn);
                echo "<script>alert('Could not complete the sale — stock may have just changed. Please try again.');</script>";
            }
        }
    }
}

$sql = "SELECT * FROM customers";
$result = mysqli_query($conn, $sql);
$customer_count = mysqli_num_rows($result);

$sql = "SELECT * FROM products";
$result1 = mysqli_query($conn, $sql);
$product_count = mysqli_num_rows($result1);
?>

<div class="admin-container">
    <?php if($_SESSION['role'] == 'admin'){
    include "../includes/admin_sidebar.php";
}
elseif($_SESSION['role'] == 'sales'){
    include "../includes/sales_sidebar.php";
} ?>

    <div class="view py-4 px-4">

        <!-- Breadcrumb / back link -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold text-success mb-1"><i class="fa-solid fa-cart-plus me-2"></i>Add Sale</h3>
                <p class="text-muted mb-0">Record a new sale for a customer.</p>
            </div>
           
        </div>

        <div class="row g-4">
            <!-- Form Card -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4">

                    <?php if ($customer_count == 0): ?>
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            No customers found. <a href="../customers/add_customer.php" class="ms-1 fw-semibold">Add a customer first</a>.
                        </div>
                    <?php endif; ?>

                    <?php if ($product_count == 0): ?>
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            No products found. <a href="../products/add_product.php" class="ms-1 fw-semibold">Add a product first</a>.
                        </div>
                    <?php endif; ?>

                    <form action="view_sales.php" method="POST" id="addSaleForm">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark"><i class="fa-solid fa-handshake text-success me-1"></i> Customer</label>
                            <select name="customer_id" id="customer_id" class="form-select" required>
                                <option value="">Select Customer</option>
                                <?php
                                while($cust = mysqli_fetch_assoc($result)){
                                    echo "<option value='".$cust['customer_id']."'>".htmlspecialchars($cust['name'])."</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark"><i class="fa-solid fa-box-open text-success me-1"></i> Product</label>
                            <select name="product_id" id="product_id" class="form-select" required>
                                <option value="">Select Product</option>
                                <?php
                                while($prod = mysqli_fetch_assoc($result1)){
                                    $disabled = $prod['stock'] <= 0 ? "disabled" : "";
                                    echo "<option value='".$prod['product_id']."' data-price='".$prod['price']."' data-stock='".$prod['stock']."' $disabled>"
                                        .htmlspecialchars($prod['product_name'])." — Rs. ".number_format($prod['price'],2)." (Stock: ".$prod['stock'].($prod['stock']<=0 ? ' — Out of Stock' : '').")"
                                        ."</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark"><i class="fa-solid fa-hashtag text-success me-1"></i> Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="1" required>
                            <div id="stockWarning" class="form-text text-danger d-none">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Requested quantity exceeds available stock.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark"><i class="fa-solid fa-calendar-days text-success me-1"></i> Sale Date</label>
                            <input type="date" name="sale_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 fw-semibold" <?php echo ($customer_count == 0 || $product_count == 0) ? 'disabled' : ''; ?>>
                            <i class="fa-solid fa-circle-check me-2"></i> Add Sale
                        </button>
                    </form>
                </div>
            </div>

            <!-- Live Summary Card -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
                    <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-receipt text-success me-2"></i>Order Summary</h5>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">Unit Price</span>
                        <span class="fw-semibold" id="summaryPrice">Rs 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">Available Stock</span>
                        <span class="fw-semibold" id="summaryStock">—</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">Quantity</span>
                        <span class="fw-semibold" id="summaryQty">1</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2">
                        <span class="fw-bold text-dark">Estimated Total</span>
                        <span class="fw-bold text-success fs-5" id="summaryTotal">Rs 0.00</span>
                    </div>
                    <small class="text-muted mt-3"><i class="fa-solid fa-circle-info me-1"></i>Final total is calculated and saved automatically on submit.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const stockWarning = document.getElementById('stockWarning');
    const submitBtn = document.querySelector('#addSaleForm button[type="submit"]');

    const summaryPrice = document.getElementById('summaryPrice');
    const summaryStock = document.getElementById('summaryStock');
    const summaryQty = document.getElementById('summaryQty');
    const summaryTotal = document.getElementById('summaryTotal');

    function updateSummary() {
        const selected = productSelect.options[productSelect.selectedIndex];
        const price = parseFloat(selected?.getAttribute('data-price')) || 0;
        const stock = parseInt(selected?.getAttribute('data-stock')) || 0;
        const qty = parseInt(quantityInput.value) || 0;

        summaryPrice.textContent = 'Rs ' + price.toFixed(2);
        summaryStock.textContent = selected && selected.value ? stock : '—';
        summaryQty.textContent = qty;
        summaryTotal.textContent = 'Rs ' + (price * qty).toFixed(2);

        const overselling = selected && selected.value && qty > stock;
        stockWarning.classList.toggle('d-none', !overselling);
        if (submitBtn) submitBtn.disabled = overselling;
    }

    productSelect.addEventListener('change', updateSummary);
    quantityInput.addEventListener('input', updateSummary);
    updateSummary();
});
</script>

<?php include "../includes/footer.php"; ?>