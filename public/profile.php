<?php
/**
 * Profile Page
 */

require_once __DIR__ . '/../core/App.php';
App::init();

Session::requireLogin();

$userModel = new User();
$userId = Session::getUserId();
$error = '';
$success = '';

$user = $userModel->findById($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    
    if (empty($username) || empty($email)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email';
    } elseif ($userModel->emailExists($email, $userId)) {
        $error = 'Email already registered';
    } else {
        if ($userModel->update($userId, $username, $email)) {
            Session::set('username', $username);
            $success = 'Profile updated successfully';
            $user['username'] = $username;
            $user['email'] = $email;
        } else {
            $error = 'An error occurred. Please try again.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    if ($userModel->delete($userId)) {
        Session::destroy();
        header('Location: index.php?deleted=1');
        exit();
    } else {
        $error = 'An error occurred. Please try again.';
    }
}

$pageTitle = 'Profile - BudgetPlanner';
require_once __DIR__ . '/../views/layouts/header.php';
?>

<div class="row row-cards">
    <div class="col-lg-4">
        <!-- Profile Card -->
        <div class="card">
            <div class="card-body text-center py-4">
                <span class="avatar avatar-xl mb-3 avatar-rounded bg-primary-lt">
                    <span class="fs-1"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></span>
                </span>
                <h3 class="m-0 mb-1"><?php echo htmlspecialchars($user['username']); ?></h3>
                <div class="text-secondary"><?php echo htmlspecialchars($user['email']); ?></div>
                <div class="mt-3">
                    <span class="badge bg-primary-lt">
                        <i class="ti ti-calendar me-1"></i>
                        Member since <?php echo date('M Y', strtotime($user['created_date'])); ?>
                    </span>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <div class="d-flex">
                    <a href="dashboard.php" class="btn btn-link">
                        <i class="ti ti-home me-2"></i>Dashboard
                    </a>
                    <a href="logout.php" class="btn btn-link ms-auto text-danger">
                        <i class="ti ti-logout me-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div><i class="ti ti-alert-circle me-2"></i></div>
                    <div><?php echo htmlspecialchars($error); ?></div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div><i class="ti ti-check me-2"></i></div>
                    <div><?php echo htmlspecialchars($success); ?></div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        <?php endif; ?>

        <!-- Profile Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-user me-2"></i>Profile Information
                </h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="username" 
                                   value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="card border-danger">
            <div class="card-status-top bg-danger"></div>
            <div class="card-header">
                <h3 class="card-title text-danger">
                    <i class="ti ti-alert-triangle me-2"></i>Danger Zone
                </h3>
            </div>
            <div class="card-body">
                <p class="text-secondary mb-3">
                    Deleting your account will permanently remove all your data including categories, 
                    transactions, and budgets. This action cannot be undone.
                </p>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="ti ti-trash me-2"></i>Delete Account
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal modal-blur fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <i class="ti ti-alert-triangle text-danger mb-2" style="font-size: 3rem;"></i>
                <h3>Are you sure?</h3>
                <div class="text-secondary">
                    This will permanently delete your account and all associated data:
                    <ul class="text-start mt-2 text-danger">
                        <li>Your profile information</li>
                        <li>All your categories</li>
                        <li>All your transactions</li>
                        <li>All your budgets</li>
                    </ul>
                    <strong>This action cannot be undone.</strong>
                </div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                        <div class="col">
                            <form method="POST">
                                <button type="submit" name="delete_account" class="btn btn-danger w-100">
                                    <i class="ti ti-trash me-2"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../views/layouts/footer.php'; ?>
