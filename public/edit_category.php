<?php
/**
 * Edit Category Page
 */

require_once __DIR__ . '/../core/App.php';
App::init();

Session::requireLogin();

$categoryModel = new Category();
$userId = Session::getUserId();
$error = '';

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($category_id <= 0) {
    header('Location: categories.php?error=Invalid category');
    exit();
}

$category = $categoryModel->findByIdAndUser($category_id, $userId);

if (!$category) {
    header('Location: categories.php?error=Category not found');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = trim($_POST['category_name'] ?? '');
    $type = $_POST['type'] ?? '';
    
    if (empty($category_name)) {
        $error = 'Please enter a category name';
    } elseif (!in_array($type, ['income', 'expense'])) {
        $error = 'Please select a category type';
    } else {
        try {
            $categoryModel->update($category_id, $userId, $category_name, $type);
            header('Location: categories.php?success=updated');
            exit();
        } catch (PDOException $e) {
            $error = 'An error occurred. Please try again.';
        }
    }
    
    $category['category_name'] = $category_name;
    $category['type'] = $type;
}

$pageTitle = 'Edit Category - BudgetPlanner';
require_once __DIR__ . '/../views/layouts/header.php';
?>

<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <a href="categories.php" class="btn btn-ghost-secondary btn-sm">
                <i class="ti ti-arrow-left me-2"></i>Back to Categories
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

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-edit me-2"></i>Edit Category
                </h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label required">Category Name</label>
                        <input type="text" class="form-control" name="category_name" 
                               placeholder="e.g., Groceries, Salary"
                               value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Category Type</label>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="type" value="income" class="form-selectgroup-input"
                                           <?php echo ($category['type'] === 'income') ? 'checked' : ''; ?>>
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
                                           <?php echo ($category['type'] === 'expense') ? 'checked' : ''; ?>>
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

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-2"></i>Save Changes
                        </button>
                        <a href="categories.php" class="btn btn-ghost-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../views/layouts/footer.php'; ?>
