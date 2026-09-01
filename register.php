<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/validation.php';

if (is_logged_in()) {
    header('Location: ' . app_url('dashboard.php'));
    exit;
}

$page_title = 'Create Account';
$errors = [];
$successMessage = '';
$formValues = [
    'full_name' => '',
    'username' => '',
    'email' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    $formValues = [
        'full_name' => $fullName,
        'username' => $username,
        'email' => $email,
    ];

    $errors = validate_registration([
        'full_name' => $fullName,
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'confirm_password' => $confirmPassword,
    ]);

    if ($errors === []) {
        $usernameCheck = $db->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $usernameCheck->bind_param('s', $username);
        $usernameCheck->execute();
        $usernameCheck->store_result();
        if ($usernameCheck->num_rows > 0) {
            $errors['username'] = 'That username is already taken.';
        }
        $usernameCheck->close();

        $emailCheck = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $emailCheck->bind_param('s', $email);
        $emailCheck->execute();
        $emailCheck->store_result();
        if ($emailCheck->num_rows > 0) {
            $errors['email'] = 'That email is already registered.';
        }
        $emailCheck->close();
    }

    if ($errors === []) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $insert = $db->prepare(
            'INSERT INTO users (full_name, username, email, password) VALUES (?, ?, ?, ?)'
        );
        $insert->bind_param('ssss', $fullName, $username, $email, $passwordHash);

        try {
            $insert->execute();
            $successMessage = 'Your account was created. You can now log in.';
            $formValues = ['full_name' => '', 'username' => '', 'email' => ''];
        } catch (mysqli_sql_exception $exception) {
            $errors['form'] = 'That username or email is already in use.';
        }

        $insert->close();
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="auth-card">
    <div class="section-heading">
        <p class="eyebrow">Get started</p>
        <h1>Create your account</h1>
        <p>Register to keep your own tasks in one place.</p>
    </div>

    <?php if ($successMessage !== ''): ?>
        <div class="alert alert--success" role="status">
            <?= escape_html($successMessage) ?>
            <a href="<?= escape_html(app_url('login.php')) ?>">Log in now</a>
        </div>
    <?php endif; ?>

    <?php if (isset($errors['form'])): ?>
        <div class="alert alert--error" role="alert"><?= escape_html($errors['form']) ?></div>
    <?php endif; ?>

    <form method="post" class="form-stack" novalidate>
        <div class="form-field">
            <label for="full_name">Full Name</label>
            <input id="full_name" name="full_name" type="text" value="<?= escape_html($formValues['full_name']) ?>" required>
            <?php if (isset($errors['full_name'])): ?><p class="field-error"><?= escape_html($errors['full_name']) ?></p><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" value="<?= escape_html($formValues['username']) ?>" required>
            <?php if (isset($errors['username'])): ?><p class="field-error"><?= escape_html($errors['username']) ?></p><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="<?= escape_html($formValues['email']) ?>" required>
            <?php if (isset($errors['email'])): ?><p class="field-error"><?= escape_html($errors['email']) ?></p><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
            <?php if (isset($errors['password'])): ?><p class="field-error"><?= escape_html($errors['password']) ?></p><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="confirm_password">Confirm Password</label>
            <input id="confirm_password" name="confirm_password" type="password" required>
            <?php if (isset($errors['confirm_password'])): ?><p class="field-error"><?= escape_html($errors['confirm_password']) ?></p><?php endif; ?>
        </div>

        <button class="button button--full" type="submit">Create Account</button>
    </form>

    <p class="form-footer">Already have an account? <a href="<?= escape_html(app_url('login.php')) ?>">Log in</a>.</p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
