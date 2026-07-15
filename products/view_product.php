<?php
include "../database/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    $sql = "insert into products (product_name,category,price,stock,description) values(?,?,?,?,?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdis", $product_name, $category, $price, $stock, $description);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        echo "<script>alert('Product added successfully');</script>";
        header("Location: view_product.php");
        exit();
    } else {
        die("Error: " . mysqli_stmt_error($stmt));
    }
}

$sql = "SELECT * FROM products ORDER BY product_id DESC";
$result = mysqli_query($conn, $sql);
$total_products = mysqli_num_rows($result);
include "../includes/header.php";
?>

<div class="admin-container">
    <?php
    if ($_SESSION['role'] == 'admin') {
        include "../includes/admin_sidebar.php";
    } elseif ($_SESSION['role'] == 'sales') {
        include "../includes/sales_sidebar.php";
    } elseif ($_SESSION['role'] == 'hr') {
        include "../includes/hr_sidebar.php";
    }
    ?>

    <div class="view py-4 px-4">
        <?php
        $ph_icon = 'fa-box-open';
        $ph_title = 'Products';
        $ph_subtitle = $total_products . ' item' . ($total_products == 1 ? '' : 's') . ' in catalog';
        if ($_SESSION['role'] == 'admin') {
            $ph_action_link = 'add_product.php';
            $ph_action_label = 'Add Product';
            $ph_action_icon = 'fa-plus';
        }
        include "../includes/page_header.php";
        ?>

        <?php if ($total_products > 0): ?>
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table-container">
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <th colspan="2">Actions</th>
                            <?php endif; ?>
                        </tr>
                        <?php while ($p = mysqli_fetch_assoc($result)):
                            $stock = (int)$p['stock'];
                            if ($stock <= 5) {
                                $badge = 'bg-danger bg-opacity-10 text-danger border border-danger-subtle';
                                $label = 'Low Stock';
                                $icon = 'fa-triangle-exclamation';
                            } elseif ($stock <= 20) {
                                $badge = 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle';
                                $label = 'Limited';
                                $icon = 'fa-clock';
                            } else {
                                $badge = 'bg-success bg-opacity-10 text-success border border-success-subtle';
                                $label = 'In Stock';
                                $icon = 'fa-circle-check';
                            }
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-2"
                                         style="width: 36px; height: 36px; flex-shrink: 0;">
                                        <i class="fa-solid fa-box"></i>
                                    </div>
                                    <span class="fw-semibold text-dark"><?php echo htmlspecialchars($p['product_name']); ?></span>
                                </div>
                            </td>
                            <td><span class="text-muted"><?php echo htmlspecialchars($p['category'] ?: '—'); ?></span></td>
                            <td><span class="fw-bold text-dark">Rs <?php echo number_format($p['price'], 2); ?></span></td>
                            <td><?php echo $stock; ?> units</td>
                            <td>
                                <span class="badge <?php echo $badge; ?> rounded-pill px-2 py-1">
                                    <i class="fa-solid <?php echo $icon; ?> me-1"></i><?php echo $label; ?>
                                </span>
                            </td>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <td class="edit-btn"><a href="edit_product.php?id=<?php echo $p['product_id']; ?>"><i class="fa-solid fa-pen me-1"></i>Edit</a></td>
                                <td class="delete-btn">
                                    <a href="delete_product.php?id=<?php echo $p['product_id']; ?>"
                                       onclick="return confirm('Are you sure you want to delete this product?');">
                                        <i class="fa-solid fa-trash me-1"></i>Delete
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-3">
                <?php
                $es_icon = 'fa-box-open';
                $es_title = 'No products yet';
                $es_desc = 'Your product catalog is empty. Add your first product to start tracking inventory and sales.';
                if ($_SESSION['role'] == 'admin') {
                    $es_action_link = 'add_product.php';
                    $es_action_label = 'Add Product';
                }
                include "../includes/empty_state.php";
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include "../includes/footer.php"; ?>