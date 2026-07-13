<?php
include "../includes/header.php";
include "../database/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $sql = "insert into customers (name,phone,email,address) values(?,?,?,?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $name, $phone, $email, $address);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        echo "<script>alert('Customer added successfully');</script>";
        header("Location: view_customer.php");
        exit();
    } else {
        die("Error: " . mysqli_stmt_error($stmt));
    }
}

$sql = "SELECT * FROM customers ORDER BY customer_id DESC";
$result = mysqli_query($conn, $sql);
$total_customers = mysqli_num_rows($result);
?>

<div class="admin-container">
    <?php
    if ($_SESSION['role'] == 'admin') {
        include "../includes/admin_sidebar.php";
    } elseif ($_SESSION['role'] == 'sales') {
        include "../includes/sales_sidebar.php";
    }
    ?>

    <div class="view py-4 px-4">
        <?php
        $ph_icon = 'fa-handshake';
        $ph_title = 'Customers';
        $ph_subtitle = $total_customers . ' total customer' . ($total_customers == 1 ? '' : 's');
        $ph_action_link = 'add_customer.php';
        $ph_action_label = 'Add Customer';
        $ph_action_icon = 'fa-user-plus';
        include "../includes/page_header.php";
        ?>

        <?php if ($total_customers > 0): ?>
            <!-- Search -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body py-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" id="customerSearch" class="form-control bg-light border-start-0 ps-0"
                               placeholder="Search customers by name, phone or email...">
                    </div>
                </div>
            </div>

            <!-- Customer Cards -->
            <div class="row g-4" id="customerGrid">
                <?php while ($c = mysqli_fetch_assoc($result)):
                    $initial = strtoupper(substr(trim($c['name']), 0, 1));
                    $searchBlob = strtolower($c['name'] . ' ' . $c['phone'] . ' ' . $c['email']);
                ?>
                <div class="col-md-6 col-lg-4 customer-card-wrap" data-search="<?php echo htmlspecialchars($searchBlob); ?>">
                    <div class="card border-0 shadow-sm rounded-3 h-100 card-animate">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold me-3"
                                     style="width: 48px; height: 48px; font-size: 1.1rem; flex-shrink: 0;">
                                    <?php echo $initial ?: '?'; ?>
                                </div>
                                <div class="overflow-hidden">
                                    <h6 class="fw-bold text-dark mb-0 text-truncate"><?php echo htmlspecialchars($c['name']); ?></h6>
                                    <small class="text-muted">Customer #<?php echo $c['customer_id']; ?></small>
                                </div>
                            </div>

                            <div class="mb-2 text-truncate">
                                <i class="fa-solid fa-phone text-success me-2" style="width: 16px;"></i>
                                <span class="text-dark small"><?php echo htmlspecialchars($c['phone'] ?: '—'); ?></span>
                            </div>
                            <div class="mb-2 text-truncate">
                                <i class="fa-solid fa-envelope text-success me-2" style="width: 16px;"></i>
                                <span class="text-dark small"><?php echo htmlspecialchars($c['email'] ?: '—'); ?></span>
                            </div>
                            <div class="mb-3 text-truncate">
                                <i class="fa-solid fa-location-dot text-success me-2" style="width: 16px;"></i>
                                <span class="text-muted small"><?php echo htmlspecialchars($c['address'] ?: '—'); ?></span>
                            </div>

                            <div class="d-flex gap-2 pt-2 border-top">
                                <?php if (!empty($c['phone'])): ?>
                                <a href="tel:<?php echo htmlspecialchars($c['phone']); ?>" class="btn btn-sm btn-outline-success flex-fill" title="Call">
                                    <i class="fa-solid fa-phone"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (!empty($c['email'])): ?>
                                <a href="mailto:<?php echo htmlspecialchars($c['email']); ?>" class="btn btn-sm btn-outline-success flex-fill" title="Email">
                                    <i class="fa-solid fa-envelope"></i>
                                </a>
                                <?php endif; ?>
                                <a href="edit_customer.php?id=<?php echo $c['customer_id']; ?>" class="btn btn-sm btn-outline-success flex-fill" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="delete_customer.php?id=<?php echo $c['customer_id']; ?>" class="btn btn-sm btn-outline-danger flex-fill" title="Delete"
                                   onclick="return confirm('Are you sure you want to delete this customer?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <div id="noResults" class="text-center py-5 d-none">
                <i class="fa-solid fa-magnifying-glass text-muted opacity-25 mb-3" style="font-size: 3rem;"></i>
                <p class="text-muted mb-0">No customers match your search.</p>
            </div>

        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-3">
                <?php
                $es_icon = 'fa-handshake';
                $es_title = 'No customers yet';
                $es_desc = 'Start building your customer base by adding your first customer.';
                $es_action_link = 'add_customer.php';
                $es_action_label = 'Add Customer';
                include "../includes/empty_state.php";
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('customerSearch');
    if (!searchInput) return;
    const cards = document.querySelectorAll('.customer-card-wrap');
    const noResults = document.getElementById('noResults');

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        let visibleCount = 0;
        cards.forEach(card => {
            const match = card.getAttribute('data-search').includes(query);
            card.classList.toggle('d-none', !match);
            if (match) visibleCount++;
        });
        noResults.classList.toggle('d-none', visibleCount !== 0);
    });
});
</script>

<?php include "../includes/footer.php"; ?>