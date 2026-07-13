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
$total_invoices = mysqli_num_rows($result);
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
        $ph_title = 'Invoices';
        $ph_subtitle = $total_invoices . ' invoice' . ($total_invoices == 1 ? '' : 's') . ' on record';
        $ph_action_link = 'add_invoice.php';
        $ph_action_label = 'Add Invoice';
        $ph_action_icon = 'fa-plus';
        include "../includes/page_header.php";
        ?>

        <?php if ($total_invoices > 0): ?>
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="table-responsive">
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
                        <?php while ($row = mysqli_fetch_assoc($result)):
                            switch ($row['payment_status']) {
                                case 'paid':
                                    $badge = 'bg-success bg-opacity-10 text-success border border-success-subtle';
                                    $icon = 'fa-circle-check';
                                    break;
                                case 'partial':
                                    $badge = 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle';
                                    $icon = 'fa-clock';
                                    break;
                                default:
                                    $badge = 'bg-danger bg-opacity-10 text-danger border border-danger-subtle';
                                    $icon = 'fa-triangle-exclamation';
                            }
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-2"
                                         style="width: 36px; height: 36px; flex-shrink: 0;">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </div>
                                    <span class="fw-semibold text-dark">#<?php echo $row['invoice_id']; ?></span>
                                </div>
                            </td>
                            <td><span class="text-muted">#<?php echo $row['sale_id']; ?></span></td>
                            <td><span class="text-dark"><?php echo htmlspecialchars($row['customer_name']); ?></span></td>
                            <td><span class="text-muted"><?php echo htmlspecialchars($row['products_list'] ?? 'None'); ?></span></td>
                            <td><span class="text-muted"><?php echo $row['invoice_date']; ?></span></td>
                            <td><span class="fw-bold text-dark">Rs <?php echo number_format($row['total_amount'], 2); ?></span></td>
                            <td>
                                <span class="badge <?php echo $badge; ?> rounded-pill px-2 py-1">
                                    <i class="fa-solid <?php echo $icon; ?> me-1"></i><?php echo ucfirst($row['payment_status']); ?>
                                </span>
                            </td>
                            <td class="edit-btn"><a href="edit_invoice.php?id=<?php echo $row['invoice_id']; ?>"><i class="fa-solid fa-pen me-1"></i>Edit</a></td>
                            <td class="delete-btn">
                                <a href="delete_invoice.php?id=<?php echo $row['invoice_id']; ?>"
                                   onclick="return confirm('Are you sure you want to delete this invoice?');">
                                    <i class="fa-solid fa-trash me-1"></i>Delete
                                </a>
                            </td>
                            <td class="edit-btn"><a href="generate_invoice.php?id=<?php echo $row['invoice_id']; ?>" target="_blank"><i class="fa-solid fa-file-pdf me-1"></i>PDF</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-3">
                <?php
                $es_icon = 'fa-file-invoice';
                $es_title = 'No invoices yet';
                $es_desc = 'No invoices have been generated yet. Create one from a completed sale.';
                $es_action_link = 'add_invoice.php';
                $es_action_label = 'Add Invoice';
                include "../includes/empty_state.php";
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include "../includes/footer.php"; ?>