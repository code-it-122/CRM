<?php
include "../database/db.php";

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoice_id = $_POST['invoice_id'];
    $invoice_date = $_POST['invoice_date'];
    $payment_status = $_POST['payment_status'];

    $sql = "UPDATE invoices SET invoice_date = ?, payment_status = ? WHERE invoice_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $invoice_date, $payment_status, $invoice_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view_invoice.php");
        exit();
    } else {
        die("Error updating invoice: " . mysqli_stmt_error($stmt));
    }
}

// Handle fetch current details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "SELECT i.invoice_id, i.sale_id, i.invoice_date, i.payment_status, i.total_amount, c.name AS customer_name,
            GROUP_CONCAT(CONCAT(p.product_name, ' (x', si.quantity, ')') SEPARATOR ', ') AS products_list
            FROM invoices i
            JOIN sales s ON i.sale_id = s.sale_id
            JOIN customers c ON s.customer_id = c.customer_id
            LEFT JOIN sale_items si ON s.sale_id = si.sale_id
            LEFT JOIN products p ON si.product_id = p.product_id
            WHERE i.invoice_id = ?
            GROUP BY i.invoice_id";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $invoice = mysqli_fetch_assoc($result);

    if (!$invoice) {
        die("Invoice not found.");
    }
} else {
    die("No invoice ID specified.");
}

include "../includes/header.php";
?>

<div class="admin-container">
    <?php include "../includes/admin_sidebar.php"; ?>

    <div class="view py-4 px-4">
        <?php
        $ph_icon = 'fa-file-pen';
        $ph_title = 'Edit Invoice';
        $ph_subtitle = 'Update invoice date and payment status.';
        $ph_back_link = 'view_invoice.php';
        $ph_back_label = 'Back to Invoices';
        include "../includes/page_header.php";
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="edit_invoice.php" method="POST">
                            <input type="hidden" name="invoice_id" value="<?php echo $invoice['invoice_id']; ?>">

                            <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-cart-shopping me-1"></i> Sale Details
                            </h6>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Sale Info</label>
                                <input type="text" class="form-control" value="Sale #<?php echo $invoice['sale_id']; ?> - <?php echo htmlspecialchars($invoice['customer_name']); ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Total Amount</label>
                                <input type="text" class="form-control" value="Rs <?php echo number_format($invoice['total_amount'], 2); ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Included Products &amp; Quantity</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($invoice['products_list'] ?? 'None'); ?>" disabled>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-file-invoice-dollar me-1"></i> Invoice Details
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="invoice_date" class="form-label fw-semibold text-dark">Invoice Date</label>
                                    <input type="date" name="invoice_date" id="invoice_date" class="form-control" value="<?php echo $invoice['invoice_date']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="payment_status" class="form-label fw-semibold text-dark">Payment Status</label>
                                    <select name="payment_status" id="payment_status" class="form-select" required>
                                        <option value="pending" <?php echo ($invoice['payment_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="partial" <?php echo ($invoice['payment_status'] == 'partial') ? 'selected' : ''; ?>>Partial</option>
                                        <option value="paid" <?php echo ($invoice['payment_status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill py-2 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-2"></i>Update Invoice
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

<?php include "../includes/footer.php"; ?>