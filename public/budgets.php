<?php
/**
 * Budgets List Page
 */

require_once __DIR__ . '/../core/App.php';
App::init();

Session::requireLogin();

$budgetModel = new Budget();
$userId = Session::getUserId();

$budgets = $budgetModel->getAllByUser($userId);

$budgets_by_month = [];
foreach ($budgets as $budget) {
    $budgets_by_month[$budget['month_year']][] = $budget;
}

$pageTitle = 'Budgets - BudgetPlanner';
require_once __DIR__ . '/../views/layouts/header.php';
?>

<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="text-secondary">Set spending limits for your categories</span>
        </div>
        <div class="col-auto ms-auto">
            <a href="add_budget.php" class="btn btn-primary">
                <i class="ti ti-plus me-2"></i>Add Budget
            </a>
        </div>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <div class="d-flex">
            <div><i class="ti ti-check me-2"></i></div>
            <div>
                <?php 
                $msg = $_GET['success'];
                if ($msg === 'added') echo 'Budget added successfully!';
                elseif ($msg === 'updated') echo 'Budget updated successfully!';
                elseif ($msg === 'deleted') echo 'Budget deleted successfully!';
                ?>
            </div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
<?php endif; ?>

<?php if (empty($budgets)): ?>
    <div class="card">
        <div class="card-body">
            <div class="empty">
                <div class="empty-icon">
                    <i class="ti ti-pig-money"></i>
                </div>
                <p class="empty-title">No budgets set</p>
                <p class="empty-subtitle text-secondary">
                    Create budgets to track your spending limits.
                </p>
                <div class="empty-action">
                    <a href="add_budget.php" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>Set Budget
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($budgets_by_month as $month_year => $month_budgets): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-calendar me-2"></i>
                <?php echo date('F Y', strtotime($month_year . '-01')); ?>
            </h3>
        </div>
        <div class="card-body">
            <div class="row row-cards">
                <?php foreach ($month_budgets as $budget): 
                    $percentage = $budget['monthly_amount'] > 0 
                        ? min(100, ($budget['spent'] / $budget['monthly_amount']) * 100) 
                        : 0;
                    $is_over_budget = $budget['spent'] > $budget['monthly_amount'];
                    $status_color = $is_over_budget ? 'danger' : ($percentage >= 80 ? 'warning' : 'success');
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <h4 class="mb-0"><?php echo htmlspecialchars($budget['category_name']); ?></h4>
                                    <small class="text-secondary">
                                        $<?php echo number_format($budget['spent'], 2); ?> of $<?php echo number_format($budget['monthly_amount'], 2); ?>
                                    </small>
                                </div>
                                <div class="ms-auto">
                                    <div class="dropdown">
                                        <button class="btn btn-ghost-secondary btn-icon btn-sm" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="edit_budget.php?id=<?php echo $budget['budget_id']; ?>">
                                                <i class="ti ti-edit me-2"></i>Edit
                                            </a>
                                            <a class="dropdown-item text-danger" href="delete_budget.php?id=<?php echo $budget['budget_id']; ?>" 
                                               onclick="return confirm('Delete this budget?');">
                                                <i class="ti ti-trash me-2"></i>Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="progress progress-sm mb-2">
                                <div class="progress-bar bg-<?php echo $status_color; ?>" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-<?php echo $is_over_budget ? 'danger fw-bold' : 'secondary'; ?>">
                                    <?php if ($is_over_budget): ?>
                                        <i class="ti ti-alert-triangle"></i> Over by $<?php echo number_format($budget['spent'] - $budget['monthly_amount'], 2); ?>
                                    <?php else: ?>
                                        $<?php echo number_format($budget['monthly_amount'] - $budget['spent'], 2); ?> remaining
                                    <?php endif; ?>
                                </small>
                                <small class="text-secondary"><?php echo number_format($percentage, 0); ?>%</small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../views/layouts/footer.php'; ?>
