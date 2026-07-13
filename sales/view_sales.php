<?php
include "../includes/header.php";
include "../database/db.php";

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
                                    echo "<option value='".$prod['product_id']."' data-price='".$prod['price']."' data-stock='".$prod['stock']."'>"
                                        .htmlspecialchars($prod['product_name'])." — Rs".number_format($prod['price'],2)." (Stock: ".$prod['stock'].")"
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
                        <span class="fw-semibold" id="summaryPrice">Rs0.00</span>
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
                        <span class="fw-bold text-success fs-5" id="summaryTotal">Rs0.00</span>
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

    const summaryPrice = document.getElementById('summaryPrice');
    const summaryStock = document.getElementById('summaryStock');
    const summaryQty = document.getElementById('summaryQty');
    const summaryTotal = document.getElementById('summaryTotal');

    function updateSummary() {
        const selected = productSelect.options[productSelect.selectedIndex];
        const price = parseFloat(selected?.getAttribute('data-price')) || 0;
        const stock = parseInt(selected?.getAttribute('data-stock')) || 0;
        const qty = parseInt(quantityInput.value) || 0;

        summaryPrice.textContent = 'Rs' + price.toFixed(2);
        summaryStock.textContent = selected && selected.value ? stock : '—';
        summaryQty.textContent = qty;
        summaryTotal.textContent = 'Rs' + (price * qty).toFixed(2);

        if (selected && selected.value && qty > stock) {
            stockWarning.classList.remove('d-none');
        } else {
            stockWarning.classList.add('d-none');
        }
    }

    productSelect.addEventListener('change', updateSummary);
    quantityInput.addEventListener('input', updateSummary);
    updateSummary();
});
</script>

<?php include "../includes/footer.php"; ?>