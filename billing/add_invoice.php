<?php
include "../database/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sale_id = $_POST['sale_id'];
    $invoice_date = $_POST['invoice_date'];
    $payment_status = $_POST['payment_status'];
    
    // Fetch total_amount directly from sales database to be secure
    $sql = "SELECT total_amount FROM sales WHERE sale_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $sale_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($sale = mysqli_fetch_assoc($result)) {
        $total_amount = $sale['total_amount'];
    } else {
        $total_amount = 0.00;
    }

    // Insert the new invoice
    $sql = "INSERT INTO invoices (sale_id, invoice_date, payment_status, total_amount) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issd", $sale_id, $invoice_date, $payment_status, $total_amount);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                window.location.href = 'view_invoice.php';
              </script>";
        exit();
    } else {
        die("Error: " . mysqli_stmt_error($stmt));
    }
}

// Fetch all sales for dropdown selection
$sales_query = "SELECT s.sale_id, s.total_amount, c.name AS customer_name,
                GROUP_CONCAT(CONCAT(p.product_name, ' (x', si.quantity, ')') SEPARATOR ', ') AS products_list
                FROM sales s 
                JOIN customers c ON s.customer_id = c.customer_id
                LEFT JOIN sale_items si ON s.sale_id = si.sale_id
                LEFT JOIN products p ON si.product_id = p.product_id
                GROUP BY s.sale_id
                ORDER BY s.sale_id DESC";
$sales_result = mysqli_query($conn, $sales_query);

include "../includes/header.php";
?>

<div class="admin-container">
    <?php if($_SESSION['role'] == 'admin'){
    include "../includes/admin_sidebar.php";
}
elseif($_SESSION['role'] == 'sales'){
    include "../includes/sales_sidebar.php";
} ?>

    <div class="view py-4 px-4">
        <?php
        $ph_icon = 'fa-file-invoice';
        $ph_title = 'Add Invoice';
        $ph_subtitle = 'Generate a new invoice for a sale.';
        $ph_back_link = 'view_invoice.php';
        $ph_back_label = 'Back to Invoices';
        include "../includes/page_header.php";
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="add_invoice.php" method="POST">

                            <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-cart-shopping me-1"></i> Sale Details
                            </h6>
                            <div class="mb-3">
                                <label for="sale_id" class="form-label fw-semibold text-dark">Select Sale ID</label>
                                <select name="sale_id" id="sale_id" class="form-select" required>
                                    <option value="" data-amount="0.00" data-products="None">Select Sale</option>
                                    <?php
                                    while ($sale = mysqli_fetch_assoc($sales_result)) {
                                        $products = !empty($sale['products_list']) ? htmlspecialchars($sale['products_list']) : 'No products';
                                        echo "<option value='" . $sale['sale_id'] . "' data-amount='" . $sale['total_amount'] . "' data-products='" . $products . "'>";
                                        echo "Sale #" . $sale['sale_id'] . " - " . htmlspecialchars($sale['customer_name']) . " (RS" . number_format($sale['total_amount'], 2) . ")";
                                        echo "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="total_amount_display" class="form-label fw-semibold text-dark">Total Amount</label>
                                <input type="text" id="total_amount_display" class="form-control" readonly value="Rs 0.00">
                            </div>
                            <div class="mb-3">
                                <label for="products_display" class="form-label fw-semibold text-dark">Included Products &amp; Quantity</label>
                                <input type="text" id="products_display" class="form-control" readonly value="None">
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-file-invoice-dollar me-1"></i> Invoice Details
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="invoice_date" class="form-label fw-semibold text-dark">Invoice Date</label>
                                    <input type="date" name="invoice_date" id="invoice_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="payment_status" class="form-label fw-semibold text-dark">Payment Status</label>
                                    <select name="payment_status" id="payment_status" class="form-select" required>
                                        <option value="pending">Pending</option>
                                        <option value="partial">Partial</option>
                                        <option value="paid">Paid</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill py-2 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-2"></i>Generate Invoice
                                </button>
                                <a href="view_invoice.php" class="btn btn-outline-secondary py-2 px-4">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Automatically update the total amount and products fields when the Sale ID changes
document.getElementById('sale_id').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var amount = selectedOption.getAttribute('data-amount') || '0.00';
    var products = selectedOption.getAttribute('data-products') || 'None';
    var formattedAmount = parseFloat(amount).toFixed(2);
    document.getElementById('total_amount_display').value = "Rs" + formattedAmount;
    document.getElementById('products_display').value = products;
});
</script>

<?php include "../includes/footer.php"; ?>