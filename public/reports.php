<?php
/**
 * Reports Page
 */

require_once __DIR__ . '/../core/App.php';
App::init();

Session::requireLogin();

$transactionModel = new Transaction();
$userId = Session::getUserId();

$selected_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

if (!preg_match('/^\d{4}-\d{2}$/', $selected_month)) {
    $selected_month = date('Y-m');
}

$total_income = $transactionModel->getTotalIncomeByMonth($userId, $selected_month);
$total_expenses = $transactionModel->getTotalExpensesByMonth($userId, $selected_month);
$net_balance = $total_income - $total_expenses;
$category_expenses = $transactionModel->getExpensesByCategory($userId, $selected_month);
$available_months = $transactionModel->getAvailableMonths($userId);

if (!in_array(date('Y-m'), $available_months)) {
    array_unshift($available_months, date('Y-m'));
}

$pageTitle = 'Reports - BudgetPlanner';
require_once __DIR__ . '/../views/layouts/header.php';
?>

<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="text-secondary">Analyze your spending patterns</span>
        </div>
        <div class="col-auto ms-auto">
            <form method="GET" class="d-flex gap-2">
                <select name="month" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($available_months as $month): ?>
                        <option value="<?php echo $month; ?>" <?php echo $month === $selected_month ? 'selected' : ''; ?>>
                            <?php echo date('F Y', strtotime($month . '-01')); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>
</div>

<!-- Summary Stats -->
<div class="row row-deck row-cards mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card income">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="subheader text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Total Income</div>
                    <span class="badge bg-success-lt ms-auto">
                        <i class="ti ti-arrow-down-left me-1"></i>Received
                    </span>
                </div>
                <div class="h1 mb-3">$<?php echo number_format($total_income, 2); ?></div>
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-sm bg-success-lt">
                        <i class="ti ti-coins"></i>
                    </span>
                    <div class="ms-3">
                        <div class="text-secondary small"><?php echo date('F Y', strtotime($selected_month . '-01')); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card expense">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="subheader text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Total Expenses</div>
                    <span class="badge bg-danger-lt ms-auto">
                        <i class="ti ti-arrow-up-right me-1"></i>Spent
                    </span>
                </div>
                <div class="h1 mb-3 text-danger">$<?php echo number_format($total_expenses, 2); ?></div>
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-sm bg-danger-lt">
                        <i class="ti ti-shopping-cart"></i>
                    </span>
                    <div class="ms-3">
                        <div class="text-secondary small"><?php echo date('F Y', strtotime($selected_month . '-01')); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card balance">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="subheader text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Net Balance</div>
                    <span class="badge bg-<?php echo $net_balance >= 0 ? 'primary' : 'warning'; ?>-lt ms-auto">
                        <i class="ti ti-wallet me-1"></i>Balance
                    </span>
                </div>
                <div class="h1 mb-3">$<?php echo number_format($net_balance, 2); ?></div>
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-sm bg-primary-lt">
                        <i class="ti ti-<?php echo $net_balance >= 0 ? 'mood-happy' : 'alert-triangle'; ?>"></i>
                    </span>
                    <div class="ms-3">
                        <div class="text-secondary small">
                            <?php echo $net_balance >= 0 ? 'You\'re saving money!' : 'Over budget'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category Breakdown -->
<div class="row row-deck row-cards">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-chart-pie me-2"></i>Expenses by Category
                </h3>
            </div>
            <div class="card-body">
                <?php if (empty($category_expenses)): ?>
                    <div class="empty">
                        <div class="empty-icon">
                            <i class="ti ti-chart-pie-off"></i>
                        </div>
                        <p class="empty-title">No expenses recorded</p>
                        <p class="empty-subtitle text-secondary">
                            No expense transactions found for <?php echo date('F Y', strtotime($selected_month . '-01')); ?>
                        </p>
                        <div class="empty-action">
                            <a href="add_transaction.php" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Add Transaction
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                    <th class="w-50">Distribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $colors = ['blue', 'azure', 'indigo', 'purple', 'pink', 'red', 'orange', 'yellow', 'lime', 'green', 'teal', 'cyan'];
                                $i = 0;
                                foreach ($category_expenses as $category): 
                                    $percentage = $total_expenses > 0 ? ($category['total'] / $total_expenses) * 100 : 0;
                                    $color = $colors[$i % count($colors)];
                                    $i++;
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-<?php echo $color; ?>-lt me-2">
                                                <i class="ti ti-tag text-<?php echo $color; ?>"></i>
                                            </span>
                                            <span class="fw-medium"><?php echo htmlspecialchars($category['category_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-danger fw-bold">$<?php echo number_format($category['total'], 2); ?></td>
                                    <td class="text-secondary"><?php echo number_format($percentage, 1); ?>%</td>
                                    <td>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-<?php echo $color; ?>" style="width: <?php echo $percentage; ?>%"></div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-report-analytics me-2"></i>Summary
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($category_expenses)): ?>
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Total Categories</div>
                                    <div class="datagrid-content"><?php echo count($category_expenses); ?></div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Highest Expense</div>
                                    <div class="datagrid-content text-danger">
                                        $<?php echo number_format($category_expenses[0]['total'] ?? 0, 2); ?>
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Top Category</div>
                                    <div class="datagrid-content">
                                        <?php echo htmlspecialchars($category_expenses[0]['category_name'] ?? 'N/A'); ?>
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Average per Category</div>
                                    <div class="datagrid-content">
                                        $<?php echo number_format($total_expenses / max(count($category_expenses), 1), 2); ?>
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Savings Rate</div>
                                    <div class="datagrid-content <?php echo $net_balance >= 0 ? 'text-success' : 'text-danger'; ?>">
                                        <?php 
                                        $savings_rate = $total_income > 0 ? ($net_balance / $total_income) * 100 : 0;
                                        echo number_format($savings_rate, 1) . '%';
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-secondary py-4">
                                <i class="ti ti-chart-dots-3 mb-2" style="font-size: 2rem;"></i>
                                <p class="mb-0">No data available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-bolt me-2"></i>Quick Actions
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="add_transaction.php" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Add Transaction
                            </a>
                            <a href="budgets.php" class="btn btn-outline-secondary">
                                <i class="ti ti-pig-money me-2"></i>Manage Budgets
                            </a>
                            <a href="transactions.php" class="btn btn-outline-secondary">
                                <i class="ti ti-list me-2"></i>View All Transactions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../views/layouts/footer.php'; ?>
