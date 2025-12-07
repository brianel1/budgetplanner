<?php
/**
 * Categories List Page
 */

require_once __DIR__ . '/../core/App.php';
App::init();

Session::requireLogin();

$categoryModel = new Category();
$userId = Session::getUserId();

$categories = $categoryModel->getAllByUser($userId);
$income_categories = array_filter($categories, fn($c) => $c['type'] === 'income');
$expense_categories = array_filter($categories, fn($c) => $c['type'] === 'expense');

$pageTitle = 'Categories - BudgetPlanner';
require_once __DIR__ . '/../views/layouts/header.php';
?>

<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="text-secondary">Organize your transactions with categories</span>
        </div>
        <div class="col-auto ms-auto">
            <a href="add_category.php" class="btn btn-primary">
                <i class="ti ti-plus me-2"></i>Add Category
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
                if ($msg === 'added') echo 'Category added successfully!';
                elseif ($msg === 'updated') echo 'Category updated successfully!';
                elseif ($msg === 'deleted') echo 'Category deleted successfully!';
                ?>
            </div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
<?php endif; ?>

<?php if (empty($categories)): ?>
    <div class="card">
        <div class="card-body">
            <div class="empty">
                <div class="empty-icon">
                    <i class="ti ti-tags-off"></i>
                </div>
                <p class="empty-title">No categories yet</p>
                <p class="empty-subtitle text-secondary">
                    Create categories to organize your transactions.
                </p>
                <div class="empty-action">
                    <a href="add_category.php" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>Add Category
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row row-cards">
        <!-- Income Categories -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-success-lt">
                    <h3 class="card-title text-success">
                        <i class="ti ti-arrow-down-left me-2"></i>Income Categories
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-success"><?php echo count($income_categories); ?></span>
                    </div>
                </div>
                <?php if (empty($income_categories)): ?>
                    <div class="card-body text-center py-4">
                        <span class="text-secondary">No income categories</span>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($income_categories as $category): ?>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar avatar-sm bg-success-lt">
                                        <i class="ti ti-tag text-success"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <span class="fw-medium"><?php echo htmlspecialchars($category['category_name']); ?></span>
                                </div>
                                <div class="col-auto">
                                    <a href="edit_category.php?id=<?php echo $category['category_id']; ?>" class="btn btn-ghost-primary btn-icon btn-sm">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a href="delete_category.php?id=<?php echo $category['category_id']; ?>" 
                                       class="btn btn-ghost-danger btn-icon btn-sm"
                                       onclick="return confirm('Delete this category?');">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Expense Categories -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-danger-lt">
                    <h3 class="card-title text-danger">
                        <i class="ti ti-arrow-up-right me-2"></i>Expense Categories
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-danger"><?php echo count($expense_categories); ?></span>
                    </div>
                </div>
                <?php if (empty($expense_categories)): ?>
                    <div class="card-body text-center py-4">
                        <span class="text-secondary">No expense categories</span>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($expense_categories as $category): ?>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar avatar-sm bg-danger-lt">
                                        <i class="ti ti-tag text-danger"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <span class="fw-medium"><?php echo htmlspecialchars($category['category_name']); ?></span>
                                </div>
                                <div class="col-auto">
                                    <a href="edit_category.php?id=<?php echo $category['category_id']; ?>" class="btn btn-ghost-primary btn-icon btn-sm">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a href="delete_category.php?id=<?php echo $category['category_id']; ?>" 
                                       class="btn btn-ghost-danger btn-icon btn-sm"
                                       onclick="return confirm('Delete this category?');">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../views/layouts/footer.php'; ?>
