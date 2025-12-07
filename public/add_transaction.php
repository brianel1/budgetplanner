<?php
/**
 * Add Transaction Page
 */

require_once __DIR__ . '/../core/App.php';
App::init();

Session::requireLogin();

$categoryModel = new Category();
$transactionModel = new Transaction();
$userId = Session::getUserId();
$error = '';

$categories = $categoryModel->getAllByUser($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $transaction_date = $_POST['transaction_date'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $type = $_POST['type'] ?? '';
    
    if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
        $error = 'Please enter a valid amount';
    } elseif (empty($category_id)) {
        $error = 'Please select a category';
    } elseif (empty($transaction_date)) {
        $error = 'Please select a date';
    } elseif (!in_array($type, ['income', 'expense'])) {
        $error = 'Please select a transaction type';
    } elseif (!$categoryModel->belongsToUser($category_id, $userId)) {
        $error = 'Invalid category selected';
    } else {
        try {
            $transactionModel->create($userId, $category_id, $amount, $transaction_date, $description, $type);
            header('Location: transactions.php?success=added');
            exit();
        } catch (PDOException $e) {
            $error = 'An error occurred. Please try again.';
        }
    }
}

$pageTitle = 'Add Transaction - BudgetPlanner';
require_once __DIR__ . '/../views/layouts/header.php';
?>

<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <a href="transactions.php" class="btn btn-ghost-secondary btn-sm">
                <i class="ti ti-arrow-left me-2"></i>Back to Transactions
            </a>
        </div>
    </div>
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
                    <i class="ti ti-alert-circle"></i>
                </div>
                <p class="empty-title">No Categories Found</p>
                <p class="empty-subtitle text-secondary">
                    You need to create a category first.
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
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transaction Details</h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <!-- Transaction Type -->
                        <div class="mb-3">
                            <label class="form-label required">Transaction Type</label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="type" value="income" class="form-selectgroup-input"
                                               <?php echo (isset($_POST['type']) && $_POST['type'] === 'income') ? 'checked' : ''; ?>>
                                        <span class="form-selectgroup-label d-flex align-items-center p-3">
                                            <span class="me-3">
                                                <span class="form-selectgroup-check"></span>
                                            </span>
                                            <span class="form-selectgroup-label-content">
                                                <span class="form-selectgroup-title strong mb-1 text-success">
                                                    <i class="ti ti-arrow-down-left me-1"></i>Income
                                                </span>
                                                <span class="d-block text-secondary">Money coming in</span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="type" value="expense" class="form-selectgroup-input"
                                               <?php echo (isset($_POST['type']) && $_POST['type'] === 'expense') ? 'checked' : ''; ?>>
                                        <span class="form-selectgroup-label d-flex align-items-center p-3">
                                            <span class="me-3">
                                                <span class="form-selectgroup-check"></span>
                                            </span>
                                            <span class="form-selectgroup-label-content">
                                                <span class="form-selectgroup-title strong mb-1 text-danger">
                                                    <i class="ti ti-arrow-up-right me-1"></i>Expense
                                                </span>
                                                <span class="d-block text-secondary">Money going out</span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" name="amount" step="0.01" min="0.01"
                                               placeholder="0.00" value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Date</label>
                                    <input type="date" class="form-control" name="transaction_date"
                                           value="<?php echo htmlspecialchars($_POST['transaction_date'] ?? date('Y-m-d')); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Category</label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Select a category</option>
                                <?php 
                                $current_type = '';
                                foreach ($categories as $category): 
                                    if ($current_type !== $category['type']):
                                        if ($current_type !== '') echo '</optgroup>';
                                        $current_type = $category['type'];
                                        echo '<optgroup label="' . ucfirst($current_type) . '">';
                                    endif;
                                ?>
                                    <option value="<?php echo $category['category_id']; ?>"
                                            <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php if ($current_type !== '') echo '</optgroup>'; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description <span class="form-label-description">Optional</span></label>
                            <textarea class="form-control" name="description" rows="3"
                                      placeholder="Add a note about this transaction"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-check me-2"></i>Add Transaction
                            </button>
                            <a href="transactions.php" class="btn btn-ghost-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../views/layouts/footer.php'; ?>
