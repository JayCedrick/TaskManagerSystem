<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: ' . app_url('dashboard.php'));
    exit;
}

$page_title = 'Log In';
$errorMessage = '';
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim((string) ($_POST['identifier'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($identifier === '' || $password === '') {
        $errorMessage = 'Username or email and password are required.';
    } else {
        $loginIdentifier = $identifier;
        $statement = $db->prepare(
            'SELECT id, full_name, password FROM users WHERE username = ? OR email = ? LIMIT 1'
        );
        $statement->bind_param('ss', $identifier, $loginIdentifier);
        $statement->execute();
        $result = $statement->get_result();
        $user = $result->fetch_assoc();
        $statement->close();

        if ($user !== null && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            header('Location: ' . app_url('dashboard.php'));
            exit;
        }

        $errorMessage = 'Invalid username/email or password.';
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="auth-card">
    <div class="section-heading">
        <p class="eyebrow">Welcome back</p>
        <h1>Log in</h1>
        <p>Use your username or email to access your tasks.</p>
    </div>

    <?php if ($errorMessage !== ''): ?>
        <div class="alert alert--error" role="alert"><?= escape_html($errorMessage) ?></div>
    <?php endif; ?>

    <form method="post" class="form-stack" novalidate>
        <div class="form-field">
            <label for="identifier">Username or Email</label>
            <input id="identifier" name="identifier" type="text" value="<?= escape_html($identifier) ?>" autocomplete="username" required>
        </div>

        <div class="form-field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
        </div>

        <button class="button button--full" type="submit">Log In</button>
    </form>

    <p class="form-footer">No account yet? <a href="<?= escape_html(app_url('register.php')) ?>">Create one</a>.</p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
