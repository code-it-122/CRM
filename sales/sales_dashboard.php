<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sales') {
    header("Location: ../auth/login.php");
    exit();
}

$page_title = "Sales Dashboard";
include "../includes/header.php";
include "../database/db.php";

// Fetch Stats Counts
$q = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
$total_products = ($r = mysqli_fetch_assoc($q)) ? $r['total'] : 0;

$q = mysqli_query($conn, "SELECT COUNT(*) as total FROM customers");
$total_customers = ($r = mysqli_fetch_assoc($q)) ? $r['total'] : 0;

$q = mysqli_query($conn, "SELECT COUNT(*) as total_sales, SUM(total_amount) as total_rev FROM sales");
$sales_data = mysqli_fetch_assoc($q);
$total_sales = $sales_data['total_sales'] ?? 0;
$total_revenue = $sales_data['total_rev'] ?? 0.00;

// Fetch Monthly Sales Chart Data
$sales_history_q = mysqli_query($conn, "
    SELECT DATE_FORMAT(sale_date, '%b %Y') as month, COUNT(*) as count, SUM(total_amount) as amount 
    FROM sales 
    GROUP BY DATE_FORMAT(sale_date, '%Y-%m') 
    ORDER BY sale_date ASC 
    LIMIT 6
");
$chart_months = [];
$chart_sales_counts = [];
$chart_sales_amounts = [];
while ($row = mysqli_fetch_assoc($sales_history_q)) {
    $chart_months[] = $row['month'];
    $chart_sales_counts[] = intval($row['count']);
    $chart_sales_amounts[] = floatval($row['amount']);
}
if (empty($chart_months)) {
    $chart_months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    $chart_sales_counts = [0, 0, 0, 0, 0, 0];
    $chart_sales_amounts = [0, 0, 0, 0, 0, 0];
}

// Fetch Top Selling Products Data for Doughnut Chart
$top_prod_q = mysqli_query($conn, "
    SELECT p.product_name, SUM(si.quantity) as total_qty 
    FROM sale_items si 
    JOIN products p ON si.product_id = p.product_id 
    GROUP BY si.product_id 
    ORDER BY total_qty DESC 
    LIMIT 5
");
$chart_products = [];
$chart_prod_qtys = [];
while ($row = mysqli_fetch_assoc($top_prod_q)) {
    $chart_products[] = $row['product_name'];
    $chart_prod_qtys[] = intval($row['total_qty']);
}
if (empty($chart_products)) {
    $chart_products = ['No Sales'];
    $chart_prod_qtys = [0];
}
?>

<div class="admin-container">
    <?php include "../includes/sales_sidebar.php"; ?>

    <div class="view py-4 px-4">
        <!-- Welcome Card -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h3 class="fw-bold text-success mb-1">Welcome back, Sales Agent!</h3>
                    <p class="text-muted mb-0">Track customer purchases, inventory stock, and close deals.</p>
                </div>
                <div>
                    <a href="../sales/add_sales.php" class="btn btn-success d-flex align-items-center"><i class="fa-solid fa-cart-plus me-2"></i> Record New Sale</a>
                </div>
            </div>
        </div>

        <!-- Stats Cards Grid -->
        <div class="row g-4 mb-4">
            <!-- Total Customers -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-primary border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Total Customers</h6>
                            <h3 class="mb-0 text-dark fw-bold"><?php echo $total_customers; ?></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-handshake fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-info border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Total Products</h6>
                            <h3 class="mb-0 text-dark fw-bold"><?php echo $total_products; ?></h3>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-box-open fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Sales -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-warning border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Total Sales</h6>
                            <h3 class="mb-0 text-dark fw-bold"><?php echo $total_sales; ?></h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-money-bill-wave fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-success border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Revenue Generated</h6>
                            <h3 class="mb-0 text-dark fw-bold">$<?php echo number_format($total_revenue, 2); ?></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-sack-dollar fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="row g-4 mb-4">
            <!-- Monthly Sales Trend -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                    <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-chart-line text-success me-2"></i>Sales Performance Trend</h5>
                    <div style="height: 300px;">
                        <canvas id="monthlySalesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Products Share -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                    <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-chart-pie text-success me-2"></i>Top Selling Products</h5>
                    <div style="height: 300px;">
                        <canvas id="productShareChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listings Grid -->
        <div class="row g-4">
            <!-- Recent Sales table -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
                    <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-clock-rotate-left text-success me-2"></i>Recent Sales Transactions</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Sale ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recent_sales_q = mysqli_query($conn, "
                                    SELECT s.sale_id, c.name as customer_name, s.sale_date, s.total_amount 
                                    FROM sales s 
                                    JOIN customers c ON s.customer_id = c.customer_id 
                                    ORDER BY s.sale_date DESC, s.sale_id DESC 
                                    LIMIT 5
                                ");
                                if ($recent_sales_q && mysqli_num_rows($recent_sales_q) > 0) {
                                    while ($s_row = mysqli_fetch_assoc($recent_sales_q)) {
                                        echo "<tr>";
                                        echo "<td>" . $s_row['sale_id'] . "</td>";
                                        echo "<td>" . htmlspecialchars($s_row['customer_name']) . "</td>";
                                        echo "<td>" . $s_row['sale_date'] . "</td>";
                                        echo "<td class='fw-bold text-success'>$" . number_format($s_row['total_amount'], 2) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center text-muted py-3'>No sales recorded.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Selling Products list -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
                    <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-ranking-star text-warning me-2"></i>Top Selling Products List</h5>
                    <ul class="list-group list-group-flush">
                        <?php
                        $list_top_q = mysqli_query($conn, "
                            SELECT p.product_name, SUM(si.quantity) as total_qty 
                            FROM sale_items si 
                            JOIN products p ON si.product_id = p.product_id 
                            GROUP BY si.product_id 
                            ORDER BY total_qty DESC 
                            LIMIT 5
                        ");
                        if ($list_top_q && mysqli_num_rows($list_top_q) > 0) {
                            $rank = 1;
                            while ($p_row = mysqli_fetch_assoc($list_top_q)) {
                                $badge_class = 'bg-success';
                                if ($rank == 1) $badge_class = 'bg-warning text-dark';
                                elseif ($rank == 2) $badge_class = 'bg-secondary';
                                
                                echo "<li class='list-group-item d-flex justify-content-between align-items-center py-3 px-0 border-0 border-bottom'>";
                                echo "<div>";
                                echo "<span class='badge " . $badge_class . " me-2 rounded-circle' style='width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;'>" . $rank . "</span>";
                                echo "<span class='fw-medium text-dark'>" . htmlspecialchars($p_row['product_name']) . "</span>";
                                echo "</div>";
                                echo "<span class='badge bg-light text-success fw-bold border border-success-subtle'>" . $p_row['total_qty'] . " units</span>";
                                echo "</li>";
                                $rank++;
                            }
                        } else {
                            echo "<li class='list-group-item text-center text-muted py-3 px-0'>No products sold.</li>";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Monthly sales line chart
    const trendCtx = document.getElementById('monthlySalesChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chart_months); ?>,
            datasets: [{
                label: 'Sales Revenue ($)',
                data: <?php echo json_encode($chart_sales_amounts); ?>,
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.05)',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // 2. Top products doughnut chart
    const shareCtx = document.getElementById('productShareChart').getContext('2d');
    new Chart(shareCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($chart_products); ?>,
            datasets: [{
                data: <?php echo json_encode($chart_prod_qtys); ?>,
                backgroundColor: ['#198754', '#20c997', '#0dcaf0', '#ffc107', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12 }
                }
            }
        }
    });
});
</script>

<?php include "../includes/footer.php"; ?>