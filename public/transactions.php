<?php
/**
 * Transactions List Page
 */

require_once __DIR__ . '/../core/App.php';
App::init();

Session::requireLogin();

$transactionModel = new Transaction();
$userId = Session::getUserId();

$transactions = $transactionModel->getAllByUser($userId);

$pageTitle = 'Transactions - BudgetPlanner';
require_once __DIR__ . '/../views/layouts/header.php';
?>

<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="text-secondary">Manage your income and expenses</span>
        </div>
        <div class="col-auto ms-auto">
            <a href="add_transaction.php" class="btn btn-primary">
                <i class="ti ti-plus me-2"></i>Add Transaction
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
                if ($msg === 'added') echo 'Transaction added successfully!';
                elseif ($msg === 'updated') echo 'Transaction updated successfully!';
                elseif ($msg === 'deleted') echo 'Transaction deleted successfully!';
                ?>
            </div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
<?php endif; ?>

<?php if (empty($transactions)): ?>
    <div class="card">
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
    </div>
<?php else: ?>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Transaction</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th class="text-end">Amount</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm bg-<?php echo $transaction['type'] === 'income' ? 'success' : 'danger'; ?>-lt me-2">
                                    <i class="ti ti-arrow-<?php echo $transaction['type'] === 'income' ? 'down-left' : 'up-right'; ?> text-<?php echo $transaction['type'] === 'income' ? 'success' : 'danger'; ?>"></i>
                                </span>
                                <div>
                                    <span class="badge bg-<?php echo $transaction['type'] === 'income' ? 'success' : 'danger'; ?>-lt">
                                        <?php echo ucfirst($transaction['type']); ?>
                                    </span>
                                    <?php if (!empty($transaction['description'])): ?>
                                        <div class="text-secondary small"><?php echo htmlspecialchars($transaction['description']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="text-secondary"><?php echo htmlspecialchars($transaction['category_name']); ?></td>
                        <td class="text-secondary"><?php echo date('M d, Y', strtotime($transaction['transaction_date'])); ?></td>
                        <td class="text-end fw-bold text-<?php echo $transaction['type'] === 'income' ? 'success' : 'danger'; ?>">
                            <?php echo $transaction['type'] === 'income' ? '+' : '-'; ?>$<?php echo number_format($transaction['amount'], 2); ?>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-ghost-secondary btn-icon" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="edit_transaction.php?id=<?php echo $transaction['transaction_id']; ?>">
                                        <i class="ti ti-edit me-2"></i>Edit
                                    </a>
                                    <a class="dropdown-item text-danger" href="delete_transaction.php?id=<?php echo $transaction['transaction_id']; ?>" 
                                       onclick="return confirm('Delete this transaction?');">
                                        <i class="ti ti-trash me-2"></i>Delete
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../views/layouts/footer.php'; ?>
