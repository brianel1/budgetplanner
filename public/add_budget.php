<?php
/**
 * Add Budget Page
 */

require_once __DIR__ . '/../core/App.php';
App::init();

Session::requireLogin();

$categoryModel = new Category();
$budgetModel = new Budget();
$userId = Session::getUserId();
$error = '';

$categories = $categoryModel->getByType($userId, 'expense');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $monthly_amount = isset($_POST['monthly_amount']) ? trim($_POST['monthly_amount']) : '';
    $month_year = isset($_POST['month_year']) ? trim($_POST['month_year']) : '';
    
    if ($category_id <= 0) {
        $error = 'Please select a category';
    } elseif (empty($monthly_amount) || !is_numeric($monthly_amount)) {
        $error = 'Please enter a valid amount';
    } elseif ((float)$monthly_amount <= 0) {
        $error = 'Amount must be greater than zero';
    } elseif (empty($month_year) || !preg_match('/^\d{4}-\d{2}$/', $month_year)) {
        $error = 'Please select a valid month';
    } elseif (!$categoryModel->belongsToUser($category_id, $userId)) {
        $error = 'Invalid category selected';
    } elseif ($budgetModel->exists($userId, $category_id, $month_year)) {
        $error = 'A budget already exists for this category and month';
    } else {
        try {
            $budgetModel->create($userId, $category_id, (float)$monthly_amount, $month_year);
            header('Location: budgets.php?success=added');
            exit();
        } catch (PDOException $e) {
            $error = 'An error occurred. Please try again.';
        }
    }
}

$pageTitle = 'Add Budget - BudgetPlanner';
require_once __DIR__ . '/../views/layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="mb-4">
            <a href="budgets.php" class="btn btn-link text-secondary p-0">
                <i class="ti ti-arrow-left me-2"></i>Back to Budgets
            </a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div><i class="ti ti-alert-circle me-2"></i></div>
                    <div><?php echo htmlspecialchars($error); ?></div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        <?php endif; ?>

        <?php if (empty($categories)): ?>
            <div class="card">
                <div class="card-body">
                    <div class="empty">
                        <div class="empty-icon">
                            <i class="ti ti-alert-triangle"></i>
                        </div>
                        <p class="empty-title">No Expense Categories</p>
                        <p class="empty-subtitle text-secondary">
                            Create expense categories first to set budgets
                        </p>
                        <div class="empty-action">
                            <a href="add_category.php" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Create Category
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-pig-money me-2"></i>Budget Details
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label required">Category</label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['category_id']; ?>"
                                        <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Monthly Budget Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="monthly_amount" 
                                       placeholder="0.00" step="0.01" min="0.01"
                                       value="<?php echo htmlspecialchars($_POST['monthly_amount'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label required">Month</label>
                            <input type="month" class="form-control" name="month_year" 
                                   value="<?php echo htmlspecialchars($_POST['month_year'] ?? date('Y-m')); ?>" required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-check me-2"></i>Set Budget
                            </button>
                            <a href="budgets.php" class="btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../views/layouts/footer.php'; ?>
