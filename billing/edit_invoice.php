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

    <div class="add-user">
        <h1>Edit Invoice</h1>
        <form action="edit_invoice.php" method="POST">
            <input type="hidden" name="invoice_id" value="<?php echo $invoice['invoice_id']; ?>">

            <!-- Sale Info (Read-Only) -->
            <label>Sale Info</label>
            <input type="text" value="Sale #<?php echo $invoice['sale_id']; ?> - <?php echo htmlspecialchars($invoice['customer_name']); ?>" disabled>

            <!-- Total Amount (Read-Only) -->
            <label>Total Amount</label>
            <input type="text" value="$<?php echo number_format($invoice['total_amount'], 2); ?>" disabled>

            <!-- Products & Quantity (Read-Only) -->
            <label>Included Products & Quantity</label>
            <input type="text" value="<?php echo htmlspecialchars($invoice['products_list'] ?? 'None'); ?>" disabled>

            <!-- Editable Date -->
            <label for="invoice_date">Invoice Date</label>
            <input type="date" name="invoice_date" id="invoice_date" value="<?php echo $invoice['invoice_date']; ?>" required>

            <!-- Editable Status -->
            <label for="payment_status">Payment Status</label>
            <select name="payment_status" id="payment_status" required>
                <option value="pending" <?php echo ($invoice['payment_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="partial" <?php echo ($invoice['payment_status'] == 'partial') ? 'selected' : ''; ?>>Partial</option>
                <option value="paid" <?php echo ($invoice['payment_status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
            </select>

            <button type="submit">Update Invoice</button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>