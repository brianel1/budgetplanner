<?php
/**
 * Registration Page
 */

require_once __DIR__ . '/../core/App.php';
App::init();

Session::start();

$userModel = new User();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($userModel->emailExists($email)) {
        $error = 'Email already registered';
    } else {
        try {
            $userModel->create($username, $email, $password);
            $success = 'Registration successful! You can now login.';
        } catch (PDOException $e) {
            $error = 'An error occurred. Please try again.';
        }
    }
}

$pageTitle = 'Register - BudgetPlanner';
require_once __DIR__ . '/../views/layouts/header.php';
?>

<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">
            <span class="avatar avatar-xl mb-3">
                <i class="ti ti-user-plus fs-1"></i>
            </span>
            <br>Get Started
            <div class="text-secondary fs-4 fw-normal mt-1">Create your free account</div>
        </h2>
        
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

        <form method="POST" action="register.php" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="username" placeholder="Enter your name"
                       value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" class="form-control" name="email" placeholder="your@email.com"
                       value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Min 6 characters" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm password" required>
            </div>
            <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ti ti-user-plus me-2"></i>Create account
                </button>
            </div>
        </form>
    </div>
</div>

<div class="text-center text-secondary mt-3">
    Already have an account? <a href="index.php" tabindex="-1">Sign in</a>
</div>

<?php require_once __DIR__ . '/../views/layouts/footer.php'; ?>
