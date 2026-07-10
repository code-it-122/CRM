<?php
include "../includes/header.php";
include "../database/db.php";
$sql = "SELECT i.invoice_id, i.sale_id, c.name AS customer_name, i.invoice_date, i.total_amount, i.payment_status, 
        GROUP_CONCAT(CONCAT(p.product_name, ' (x', si.quantity, ')') SEPARATOR ', ') AS products_list
        FROM invoices i
        JOIN sales s ON i.sale_id = s.sale_id
        JOIN customers c ON s.customer_id = c.customer_id
        LEFT JOIN sale_items si ON s.sale_id = si.sale_id
        LEFT JOIN products p ON si.product_id = p.product_id
        GROUP BY i.invoice_id, i.sale_id, c.name, i.invoice_date, i.total_amount, i.payment_status
        ORDER BY i.invoice_id DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="admin-container">
    <?php if($_SESSION['role'] == 'admin'){
    include "../includes/admin_sidebar.php";
}
elseif($_SESSION['role'] == 'sales'){
    include "../includes/sales_sidebar.php";
} ?>
    
    <div class="view">
        <h1>Invoices</h1>
        <hr>
        <a href="add_invoice.php" class="add-btn">Add Invoice</a>
        
        <table class="table-container">
            <tr>
                <th>Invoice ID</th>
                <th>Sale ID</th>
                <th>Customer Name</th>
                <th>Products (Qty)</th>
                <th>Invoice Date</th>
                <th>Total Amount</th>
                <th>Payment Status</th>
                <th colspan="3">Actions</th>
            </tr>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['invoice_id'] . "</td>";
                    echo "<td>" . $row['sale_id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['products_list'] ?? 'None') . "</td>";
                    echo "<td>" . $row['invoice_date'] . "</td>";
                    echo "<td>$" . number_format($row['total_amount'], 2) . "</td>";
                    echo "<td>" . ucfirst($row['payment_status']) . "</td>";
                    echo "<td class='edit-btn'><a href='edit_invoice.php?id=" . $row['invoice_id'] . "'>Edit</a></td>";
                    echo "<td class='delete-btn'>
                            <a href='delete_invoice.php?id=" . $row['invoice_id'] . "' 
                               onclick=\"return confirm('Are you sure you want to delete this invoice?');\">
                               Delete
                            </a>
                          </td>";
                          echo "<td><a href='generate_invoice.php?id=".$row['invoice_id']."' target='_blank'>Generate PDF</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No invoices found.</td></tr>";
            }
            ?>
        </table>
    </div>
</div>

<?php include "../includes/footer.php"; ?>