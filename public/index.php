<?php
/**
 * Login Page
 */

require_once __DIR__ . '/../core/App.php';
App::init();

Session::start();

if (Session::isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$userModel = new User();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email';
    } else {
        $user = $userModel->verifyPassword($email, $password);
        if ($user) {
            Session::set('user_id', $user['user_id']);
            Session::set('username', $user['username']);
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid email or password';
        }
    }
}

$pageTitle = 'Login - BudgetPlanner';
require_once __DIR__ . '/../views/layouts/header.php';
?>

<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">
            <span class="avatar avatar-xl mb-3">
                <i class="ti ti-wallet fs-1"></i>
            </span>
            <br>Welcome Back
            <div class="text-secondary fs-4 fw-normal mt-1">Sign in to BudgetPlanner</div>
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

        <form method="POST" action="index.php" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" class="form-control" name="email" placeholder="your@email.com"
                       value="<?php echo htmlspecialchars($email ?? ''); ?>" autocomplete="off" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Your password" autocomplete="off" required>
            </div>
            <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ti ti-login me-2"></i>Sign in
                </button>
            </div>
        </form>
    </div>
</div>

<div class="text-center text-secondary mt-3">
    Don't have an account? <a href="register.php" tabindex="-1">Sign up</a>
</div>

<?php require_once __DIR__ . '/../views/layouts/footer.php'; ?>
