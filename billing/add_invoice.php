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
                alert('Invoice generated successfully');
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

    <div class="add-user">
        <h1>Add Invoice</h1>
        <form action="add_invoice.php" method="POST">
            
            <label for="sale_id">Select Sale ID</label>
            <select name="sale_id" id="sale_id" required>
                <option value="" data-amount="0.00" data-products="None">Select Sale</option>
                <?php
                while ($sale = mysqli_fetch_assoc($sales_result)) {
                    $products = !empty($sale['products_list']) ? htmlspecialchars($sale['products_list']) : 'No products';
                    echo "<option value='" . $sale['sale_id'] . "' data-amount='" . $sale['total_amount'] . "' data-products='" . $products . "'>";
                    echo "Sale #" . $sale['sale_id'] . " - " . htmlspecialchars($sale['customer_name']) . " ($" . number_format($sale['total_amount'], 2) . ")";
                    echo "</option>";
                }
                ?>
            </select>

            <label for="total_amount_display">Total Amount</label>
            <input type="text" id="total_amount_display" readonly value="$0.00">

            <label for="products_display">Included Products & Quantity</label>
            <input type="text" id="products_display" readonly value="None">

            <label for="invoice_date">Invoice Date</label>
            <input type="date" name="invoice_date" id="invoice_date" value="<?php echo date('Y-m-d'); ?>" required>

            <label for="payment_status">Payment Status</label>
            <select name="payment_status" id="payment_status" required>
                <option value="pending">Pending</option>
                <option value="partial">Partial</option>
                <option value="paid">Paid</option>
            </select>

            <button type="submit">Generate Invoice</button>
        </form>
    </div>
</div>

<script>
// Automatically update the total amount and products fields when the Sale ID changes
document.getElementById('sale_id').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var amount = selectedOption.getAttribute('data-amount') || '0.00';
    var products = selectedOption.getAttribute('data-products') || 'None';
    var formattedAmount = parseFloat(amount).toFixed(2);
    document.getElementById('total_amount_display').value = "$" + formattedAmount;
    document.getElementById('products_display').value = products;
});
</script>

<?php include "../includes/footer.php"; ?>