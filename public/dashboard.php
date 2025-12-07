<?php
/**
 * Dashboard Page
 */

require_once __DIR__ . '/../core/App.php';
App::init();

Session::requireLogin();

$transactionModel = new Transaction();
$userId = Session::getUserId();

$total_income = $transactionModel->getTotalIncome($userId);
$total_expenses = $transactionModel->getTotalExpenses($userId);
$remaining_balance = $total_income - $total_expenses;
$recent_transactions = $transactionModel->getRecentByUser($userId, 5);

$pageTitle = 'Dashboard - BudgetPlanner';
require_once __DIR__ . '/../views/layouts/header.php';
?>

<!-- Stats Row -->
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
                        <div class="text-secondary small">Money coming in</div>
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
                        <div class="text-secondary small">Money going out</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-4">
        <div class="card stat-card balance">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="subheader text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Balance</div>
                    <span class="badge bg-primary-lt ms-auto">
                        <i class="ti ti-wallet me-1"></i>Available
                    </span>
                </div>
                <div class="h1 mb-3">$<?php echo number_format($remaining_balance, 2); ?></div>
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-sm bg-primary-lt">
                        <i class="ti ti-piggy-bank"></i>
                    </span>
                    <div class="ms-3">
                        <div class="text-secondary small">
                            <?php echo $remaining_balance >= 0 ? 'Looking good!' : 'Over budget'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-deck row-cards">
    <!-- Recent Transactions -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Transactions</h3>
                <div class="card-actions">
                    <a href="transactions.php" class="btn btn-primary btn-sm">
                        View All
                    </a>
                </div>
            </div>
            <?php if (empty($recent_transactions)): ?>
                <div class="card-body">
                    <div class="empty">
                        <div class="empty-icon">
                            <i class="ti ti-receipt-off"></i>
                        </div>
                        <p class="empty-title">No transactions yet</p>
                        <p class="empty-subtitle text-secondary">
                            Start tracking your finances by adding your first transaction.
                        </p>
                        <div class="empty-action">
                            <a href="add_transaction.php" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Add Transaction
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recent_transactions as $transaction): ?>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar bg-<?php echo $transaction['type'] === 'income' ? 'success' : 'danger'; ?>-lt">
                                        <i class="ti ti-arrow-<?php echo $transaction['type'] === 'income' ? 'down-left' : 'up-right'; ?> text-<?php echo $transaction['type'] === 'income' ? 'success' : 'danger'; ?>"></i>
                                    </span>
                                </div>
                                <div class="col text-truncate">
                                    <span class="text-reset d-block"><?php echo htmlspecialchars($transaction['category_name'] ?? 'Uncategorized'); ?></span>
                                    <small class="text-secondary"><?php echo date('M d, Y', strtotime($transaction['transaction_date'])); ?></small>
                                </div>
                                <div class="col-auto">
                                    <span class="fw-bold text-<?php echo $transaction['type'] === 'income' ? 'success' : 'danger'; ?>">
                                        <?php echo $transaction['type'] === 'income' ? '+' : '-'; ?>$<?php echo number_format($transaction['amount'], 2); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="row row-cards">
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
                            <a href="add_category.php" class="btn btn-outline-secondary">
                                <i class="ti ti-tag me-2"></i>New Category
                            </a>
                            <a href="add_budget.php" class="btn btn-outline-secondary">
                                <i class="ti ti-pig-money me-2"></i>Set Budget
                            </a>
                            <a href="reports.php" class="btn btn-outline-primary">
                                <i class="ti ti-chart-bar me-2"></i>View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Summary Card -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <span class="avatar avatar-md bg-primary-lt">
                                <i class="ti ti-chart-donut-3"></i>
                            </span>
                            <div class="ms-3">
                                <div class="fw-bold">Savings Rate</div>
                                <div class="text-secondary small">This period</div>
                            </div>
                            <div class="ms-auto">
                                <?php 
                                $savings_rate = $total_income > 0 ? (($total_income - $total_expenses) / $total_income) * 100 : 0;
                                $rate_color = $savings_rate >= 20 ? 'success' : ($savings_rate >= 0 ? 'warning' : 'danger');
                                ?>
                                <span class="h3 mb-0 text-<?php echo $rate_color; ?>"><?php echo number_format($savings_rate, 0); ?>%</span>
                            </div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-<?php echo $rate_color; ?>" style="width: <?php echo max(0, min(100, $savings_rate)); ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../views/layouts/footer.php'; ?>
