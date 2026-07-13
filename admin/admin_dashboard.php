<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$page_title = "Admin Dashboard";
include "../includes/header.php";
include "../database/db.php";

// 1. Fetch Summary Counts
// Total Users
$q = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$total_users = ($r = mysqli_fetch_assoc($q)) ? $r['total'] : 0;

// Total Products
$q = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
$total_products = ($r = mysqli_fetch_assoc($q)) ? $r['total'] : 0;

// Total Customers
$q = mysqli_query($conn, "SELECT COUNT(*) as total FROM customers");
$total_customers = ($r = mysqli_fetch_assoc($q)) ? $r['total'] : 0;

// Total Employees
$q = mysqli_query($conn, "SELECT COUNT(*) as total FROM employees");
$total_employees = ($r = mysqli_fetch_assoc($q)) ? $r['total'] : 0;

// Total Sales & Total Revenue
$q = mysqli_query($conn, "SELECT COUNT(*) as total_sales, SUM(total_amount) as total_revenue FROM sales");
$sales_data = mysqli_fetch_assoc($q);
$total_sales = $sales_data['total_sales'] ?? 0;
$total_revenue = $sales_data['total_revenue'] ?? 0.00;

// Pending Leaves
$q = mysqli_query($conn, "SELECT COUNT(*) as total FROM leaves WHERE status = 'pending'");
$pending_leaves = ($r = mysqli_fetch_assoc($q)) ? $r['total'] : 0;

// Low Stock Products
$q = mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE stock <= 5");
$low_stock = ($r = mysqli_fetch_assoc($q)) ? $r['total'] : 0;

// 2. Fetch Chart Data
// Monthly Sales & Revenue
$sales_overview_q = mysqli_query($conn, "
    SELECT DATE_FORMAT(sale_date, '%b %Y') as month, COUNT(*) as sales_count, SUM(total_amount) as revenue 
    FROM sales 
    GROUP BY DATE_FORMAT(sale_date, '%Y-%m') 
    ORDER BY sale_date ASC 
    LIMIT 6
");
$chart_months = [];
$chart_sales_counts = [];
$chart_revenues = [];
while ($row = mysqli_fetch_assoc($sales_overview_q)) {
    $chart_months[] = $row['month'];
    $chart_sales_counts[] = intval($row['sales_count']);
    $chart_revenues[] = floatval($row['revenue']);
}
if (empty($chart_months)) {
    $chart_months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    $chart_sales_counts = [0, 0, 0, 0, 0, 0];
    $chart_revenues = [0, 0, 0, 0, 0, 0];
}

// Monthly Customer Growth
$customer_growth_q = mysqli_query($conn, "
    SELECT DATE_FORMAT(created_at, '%b %Y') as month, COUNT(*) as customer_count 
    FROM customers 
    GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
    ORDER BY created_at ASC 
    LIMIT 6
");
$chart_cust_months = [];
$chart_cust_counts = [];
while ($row = mysqli_fetch_assoc($customer_growth_q)) {
    $chart_cust_months[] = $row['month'];
    $chart_cust_counts[] = intval($row['customer_count']);
}
if (empty($chart_cust_months)) {
    $chart_cust_months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    $chart_cust_counts = [0, 0, 0, 0, 0, 0];
}
?>

<div class="admin-container">
    <?php include "../includes/admin_sidebar.php"; ?>

    <div class="view py-4 px-4">
        <!-- Welcome Jumbotron -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h3 class="fw-bold text-success mb-1">Welcome back, Admin!</h3>
                    <p class="text-muted mb-0">Here is what is happening across your CRM platform today.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="../customers/add_customer.php" class="btn btn-success d-flex align-items-center"><i class="fa-solid fa-user-plus me-2"></i> Add Customer</a>
                    <a href="../products/add_product.php" class="btn btn-outline-success d-flex align-items-center"><i class="fa-solid fa-circle-plus me-2"></i> Add Product</a>
                </div>
            </div>
        </div>

        <!-- 8 Stat Cards in Grid -->
        <div class="row g-4 mb-4">
            <!-- Total Users -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-primary border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Total Users</h6>
                            <h3 class="mb-0 text-dark fw-bold"><?php echo $total_users; ?></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-users-gear fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Total Products -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-success border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Total Products</h6>
                            <h3 class="mb-0 text-dark fw-bold"><?php echo $total_products; ?></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-box-open fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-info border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Total Customers</h6>
                            <h3 class="mb-0 text-dark fw-bold"><?php echo $total_customers; ?></h3>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-handshake fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Employees -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-warning border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Total Employees</h6>
                            <h3 class="mb-0 text-dark fw-bold"><?php echo $total_employees; ?></h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-id-badge fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Sales -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-danger border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Total Sales</h6>
                            <h3 class="mb-0 text-dark fw-bold"><?php echo $total_sales; ?></h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-money-bill-wave fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-success border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Total Revenue</h6>
                            <h3 class="mb-0 text-dark fw-bold">$<?php echo number_format($total_revenue, 2); ?></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-sack-dollar fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Leaves -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-warning border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Pending Leaves</h6>
                            <h3 class="mb-0 text-dark fw-bold"><?php echo $pending_leaves; ?></h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-calendar-minus fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Products -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-danger border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Low Stock Items</h6>
                            <h3 class="mb-0 text-dark fw-bold"><?php echo $low_stock; ?></h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row g-4 mb-4">
            <!-- Sales & Revenue Overview -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-chart-line text-success me-2"></i>Sales & Revenue Overview</h5>
                        <small class="text-muted">Last 6 Months</small>
                    </div>
                    <div style="height: 320px;">
                        <canvas id="salesOverviewChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Customer Growth -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-user-plus text-success me-2"></i>Customer Growth</h5>
                        <small class="text-muted">Monthly</small>
                    </div>
                    <div style="height: 320px;">
                        <canvas id="customerGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Low Stock Alert Listing -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
                    <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Low Stock Alert (<= 5 units)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stock_list_q = mysqli_query($conn, "SELECT product_id, product_name, category, stock FROM products WHERE stock <= 5 ORDER BY stock ASC LIMIT 5");
                                if ($stock_list_q && mysqli_num_rows($stock_list_q) > 0) {
                                    while ($p_row = mysqli_fetch_assoc($stock_list_q)) {
                                        echo "<tr>";
                                        echo "<td>" . $p_row['product_id'] . "</td>";
                                        echo "<td>" . htmlspecialchars($p_row['product_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($p_row['category']) . "</td>";
                                        echo "<td><span class='badge bg-danger bg-opacity-10 text-danger border border-danger-subtle'>" . $p_row['stock'] . " left</span></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center text-muted py-3'>No low stock warnings.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Leaves Pending -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
                    <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-envelope text-warning me-2"></i>Pending Leave Requests</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Employee</th>
                                    <th>Type</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $leaves_list_q = mysqli_query($conn, "
                                    SELECT l.leave_id, e.name as emp_name, l.leave_type, l.from_date, l.to_date 
                                    FROM leaves l 
                                    JOIN employees e ON l.employee_id = e.employee_id 
                                    WHERE l.status = 'pending' 
                                    ORDER BY l.from_date ASC LIMIT 5
                                ");
                                if ($leaves_list_q && mysqli_num_rows($leaves_list_q) > 0) {
                                    while ($l_row = mysqli_fetch_assoc($leaves_list_q)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($l_row['emp_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($l_row['leave_type']) . "</td>";
                                        echo "<td>" . $l_row['from_date'] . "</td>";
                                        echo "<td>" . $l_row['to_date'] . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center text-muted py-3'>No pending leave requests.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart JS Setup Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Sales & Revenue line chart
    const salesCtx = document.getElementById('salesOverviewChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chart_months); ?>,
            datasets: [
                {
                    label: 'Revenue ($)',
                    data: <?php echo json_encode($chart_revenues); ?>,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    yAxisID: 'y'
                },
                {
                    label: 'Deals Count',
                    data: <?php echo json_encode($chart_sales_counts); ?>,
                    borderColor: '#0d6efd',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.35,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { boxWidth: 12, usePointStyle: true }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { drawOnChartArea: false }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: true }
                }
            }
        }
    });

    // 2. Customer growth bar chart
    const custCtx = document.getElementById('customerGrowthChart').getContext('2d');
    new Chart(custCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_cust_months); ?>,
            datasets: [{
                label: 'New Customers',
                data: <?php echo json_encode($chart_cust_counts); ?>,
                backgroundColor: '#198754',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>

<?php include "../includes/footer.php"; ?>