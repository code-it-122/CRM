<?php
include "../includes/header.php";
include "../database/db.php";
?>

<div class="admin-container">
    <?php 
    if ($_SESSION['role'] == 'admin') {
        include "../includes/admin_sidebar.php";
    } 
    elseif ($_SESSION['role'] == 'sales') {
        include "../includes/sales_sidebar.php";
    }
    elseif ($_SESSION['role'] == 'hr') {
        include "../includes/hr_sidebar.php";
    }

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
    <div class="view">
       
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
                    label: 'Revenue (Rs)',
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