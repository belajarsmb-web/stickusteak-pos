<?php
/**
 * Stickusteak POS - Reports & Analytics
 * Premium Black & Gold Theme
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}

$username = $_SESSION['username'] ?? 'User';
$page_title = "Reports";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Stickusteak POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="/php-native/assets/css/premium-theme.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .report-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: slideUp 0.6s ease-out;
        }
        
        .report-card:hover {
            transform: translateY(-5px);
            border-color: var(--gold-primary);
            box-shadow: 0 15px 40px rgba(212,175,55,0.2);
        }
        
        .report-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }
        
        .report-icon.primary { 
            background: rgba(212,175,55,0.15); 
            color: var(--gold-primary); 
        }
        
        .report-icon.success { 
            background: rgba(40,167,69,0.15); 
            color: #28a745; 
        }
        
        .report-icon.warning { 
            background: rgba(255,193,7,0.15); 
            color: #ffc107; 
        }
        
        .report-icon.danger { 
            background: rgba(220,53,69,0.15); 
            color: #dc3545; 
        }
        
        .report-value {
            font-size: 2rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 5px;
        }
        
        .report-label {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .filter-section {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 20px;
        }
        
        .top-items-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .top-items-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(212,175,55,0.1);
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }
        
        .top-items-list li:hover {
            background: rgba(212,175,55,0.05);
            border-color: rgba(212,175,55,0.3);
        }
        
        .rank-badge {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            margin-right: 15px;
        }
        
        .rank-1 { 
            background: linear-gradient(135deg, #FFD700, #FFA500); 
            color: #000; 
        }
        
        .rank-2 { 
            background: linear-gradient(135deg, #C0C0C0, #808080); 
            color: #000; 
        }
        
        .rank-3 { 
            background: linear-gradient(135deg, #CD7F32, #8B4513); 
            color: #fff; 
        }
        
        .rank-other { 
            background: rgba(212,175,55,0.2); 
            color: var(--gold-light); 
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-name {
            font-weight: 600;
            color: var(--gold-light);
            margin-bottom: 3px;
        }
        
        .item-details {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
        }
        
        .item-stats {
            text-align: right;
        }
        
        .item-qty {
            font-weight: 700;
            color: var(--gold-primary);
            font-size: 1.1rem;
        }
        
        .item-revenue {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
        }
        
        .category-breakdown {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .category-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(212,175,55,0.1);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .category-item:hover {
            background: rgba(212,175,55,0.05);
            border-color: rgba(212,175,55,0.3);
        }
        
        .category-name {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
            margin-bottom: 8px;
        }
        
        .category-value {
            font-size: 1.3rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            color: var(--gold-primary);
        }
        
        .category-percent {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.5);
            margin-top: 5px;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .loading-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255,255,255,0.4);
        }
        
        .loading-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="sidebar-brand">
                    🍽️ Stickusteak
                </div>
                <nav class="mt-3">
                    <a href="/php-native/pages/dashboard.php" class="nav-link">
                        <i class="bi bi-speedometer2"></i>Dashboard
                    </a>
                    <a href="/php-native/pages/pos-tables.php" class="nav-link">
                        <i class="bi bi-grid-3x3-gap"></i>POS Tables
                    </a>
                    <a href="/php-native/pages/tickets.php" class="nav-link">
                        <i class="bi bi-receipt"></i>Tickets
                    </a>
                    <a href="/php-native/pages/menu.php" class="nav-link">
                        <i class="bi bi-menu-button-wide"></i>Menu
                    </a>
                    <a href="/php-native/pages/modifiers.php" class="nav-link">
                        <i class="bi bi-ui-checks-grid"></i>Modifiers
                    </a>
                    <a href="/php-native/pages/customers.php" class="nav-link">
                        <i class="bi bi-people"></i>Customers
                    </a>
                    <a href="/php-native/pages/reports.php" class="nav-link active">
                        <i class="bi bi-graph-up"></i>Reports
                    </a>
                    <a href="/php-native/pages/users.php" class="nav-link">
                        <i class="bi bi-person-badge"></i>Users
                    </a>
                    <a href="/php-native/pages/settings.php" class="nav-link">
                        <i class="bi bi-gear"></i>Settings
                    </a>
                    <a href="/php-native/mobile/generate-qr.php" class="nav-link" target="_blank">
                        <i class="bi bi-qr-code"></i>QR Codes
                    </a>
                </nav>
                <div class="mt-auto">
                    <a href="/php-native/api/auth/logout.php" class="logout-btn">
                        <i class="bi bi-box-arrow-left"></i>Logout
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Page Header -->
                <div class="pos-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1"><i class="bi bi-graph-up me-2"></i>Reports & Analytics</h2>
                            <p class="mb-0" style="color: rgba(255,255,255,0.5); font-size: 0.9rem;">Business insights and sales analytics</p>
                        </div>
                        <button class="btn btn-refresh" onclick="loadReport()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                        </button>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="startDate">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" id="endDate">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Report Type</label>
                            <select class="form-select" id="reportType">
                                <option value="sales">Sales Report</option>
                                <option value="items">Top Items</option>
                                <option value="category">Category Breakdown</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-primary w-100" onclick="loadReport()">
                                <i class="bi bi-search me-1"></i>Generate Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row g-4 mb-4" id="summaryCards" style="display: none;">
                    <div class="col-md-3">
                        <div class="report-card">
                            <div class="report-icon primary">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="report-value" id="totalRevenue">Rp 0</div>
                            <div class="report-label">Total Revenue</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="report-card">
                            <div class="report-icon success">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <div class="report-value" id="totalOrders">0</div>
                            <div class="report-label">Total Orders</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="report-card">
                            <div class="report-icon warning">
                                <i class="bi bi-calculator"></i>
                            </div>
                            <div class="report-value" id="avgOrderValue">Rp 0</div>
                            <div class="report-label">Avg Order Value</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="report-card">
                            <div class="report-icon danger">
                                <i class="bi bi-bag"></i>
                            </div>
                            <div class="report-value" id="totalItems">0</div>
                            <div class="report-label">Items Sold</div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row g-4 mb-4" id="chartsRow" style="display: none;">
                    <div class="col-md-8">
                        <div class="report-card">
                            <h5 class="mb-3" style="color: var(--gold-light);">
                                <i class="bi bi-graph-up me-2"></i>Sales Trend
                            </h5>
                            <div class="chart-container">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="report-card">
                            <h5 class="mb-3" style="color: var(--gold-light);">
                                <i class="bi bi-pie-chart me-2"></i>Category Breakdown
                            </h5>
                            <div class="chart-container">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Items -->
                <div class="report-card" id="topItemsCard" style="display: none;">
                    <h5 class="mb-3" style="color: var(--gold-light);">
                        <i class="bi bi-star me-2"></i>Top Selling Items
                    </h5>
                    <ul class="top-items-list" id="topItemsList">
                        <!-- Items will be loaded here -->
                    </ul>
                </div>

                <!-- Loading State -->
                <div class="loading-state" id="loadingState">
                    <i class="bi bi-hourglass-split"></i>
                    <h3>Loading reports...</h3>
                    <p class="text-muted">Please wait while we generate your report</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let salesChartInstance = null;
        let categoryChartInstance = null;

        // Set default date range (last 30 days)
        document.addEventListener('DOMContentLoaded', () => {
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(startDate.getDate() - 30);
            
            document.getElementById('endDate').value = endDate.toISOString().split('T')[0];
            document.getElementById('startDate').value = startDate.toISOString().split('T')[0];
            
            loadReport();
        });

        // Load report
        async function loadReport() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const reportType = document.getElementById('reportType').value;
            
            // Show loading
            document.getElementById('loadingState').style.display = 'block';
            document.getElementById('summaryCards').style.display = 'none';
            document.getElementById('chartsRow').style.display = 'none';
            document.getElementById('topItemsCard').style.display = 'none';
            
            try {
                const response = await fetch(`/php-native/api/reports/sales.php?start=${startDate}&end=${endDate}&type=${reportType}`);
                const data = await response.json();
                
                document.getElementById('loadingState').style.display = 'none';
                
                if (data.success) {
                    updateSummaryCards(data.stats);
                    updateSalesChart(data.chartData);
                    updateCategoryChart(data.categoryData);
                    updateTopItems(data.topItems);
                    
                    document.getElementById('summaryCards').style.display = 'flex';
                    document.getElementById('chartsRow').style.display = 'flex';
                    document.getElementById('topItemsCard').style.display = 'block';
                } else {
                    alert(data.message || 'Failed to load report');
                }
            } catch (error) {
                console.error('Error loading report:', error);
                document.getElementById('loadingState').style.display = 'none';
                alert('Error loading report');
            }
        }

        // Update summary cards
        function updateSummaryCards(stats) {
            document.getElementById('totalRevenue').textContent = 'Rp ' + parseFloat(stats.total_revenue || 0).toLocaleString('id-ID');
            document.getElementById('totalOrders').textContent = (stats.total_orders || 0).toLocaleString();
            document.getElementById('avgOrderValue').textContent = 'Rp ' + parseFloat(stats.avg_order_value || 0).toLocaleString('id-ID');
            document.getElementById('totalItems').textContent = (stats.total_items || 0).toLocaleString();
        }

        // Update sales chart
        function updateSalesChart(chartData) {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            if (salesChartInstance) {
                salesChartInstance.destroy();
            }

            salesChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData?.labels || [],
                    datasets: [{
                        label: 'Revenue',
                        data: chartData?.data || [],
                        borderColor: '#D4AF37',
                        backgroundColor: 'rgba(212, 175, 55, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#D4AF37',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(26,26,26,0.9)',
                            titleColor: '#D4AF37',
                            bodyColor: '#fff',
                            borderColor: 'rgba(212,175,55,0.3)',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + parseFloat(context.parsed.y).toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(212,175,55,0.1)'
                            },
                            ticks: {
                                color: 'rgba(255,255,255,0.6)',
                                callback: function(value) {
                                    return 'Rp ' + (value/1000).toFixed(0) + 'k';
                                }
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(212,175,55,0.1)'
                            },
                            ticks: {
                                color: 'rgba(255,255,255,0.6)'
                            }
                        }
                    }
                }
            });
        }

        // Update category chart
        function updateCategoryChart(categoryData) {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            
            if (categoryChartInstance) {
                categoryChartInstance.destroy();
            }

            categoryChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: categoryData?.labels || [],
                    datasets: [{
                        data: categoryData?.data || [],
                        backgroundColor: [
                            '#D4AF37',
                            '#AA8C2C',
                            '#F4DF89',
                            '#28a745',
                            '#dc3545',
                            '#17a2b8'
                        ],
                        borderColor: 'rgba(26,26,26,1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: 'rgba(255,255,255,0.7)',
                                padding: 15,
                                font: {
                                    family: 'Poppins',
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(26,26,26,0.9)',
                            titleColor: '#D4AF37',
                            bodyColor: '#fff',
                            borderColor: 'rgba(212,175,55,0.3)',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return label + ': Rp ' + parseFloat(value).toLocaleString('id-ID') + ' (' + percent + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Update top items
        function updateTopItems(topItems) {
            const list = document.getElementById('topItemsList');
            
            if (topItems && topItems.length > 0) {
                let html = '';
                topItems.forEach((item, index) => {
                    const rank = index + 1;
                    const rankClass = rank === 1 ? 'rank-1' : rank === 2 ? 'rank-2' : rank === 3 ? 'rank-3' : 'rank-other';
                    
                    html += `
                        <li>
                            <div class="rank-badge ${rankClass}">${rank}</div>
                            <div class="item-info">
                                <div class="item-name">${item.name}</div>
                                <div class="item-details">${item.category || 'General'}</div>
                            </div>
                            <div class="item-stats">
                                <div class="item-qty">${item.quantity_sold} sold</div>
                                <div class="item-revenue">Rp ${parseFloat(item.revenue || 0).toLocaleString('id-ID')}</div>
                            </div>
                        </li>
                    `;
                });
                list.innerHTML = html;
            } else {
                list.innerHTML = '<li class="text-center text-muted" style="color: rgba(255,255,255,0.4);">No items data available</li>';
            }
        }
    </script>
</body>
</html>
