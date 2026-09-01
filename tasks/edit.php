<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/validation.php';

require_login();

$rawTaskId = $_GET['id'] ?? $_POST['id'] ?? '';
$taskId = is_scalar($rawTaskId) ? filter_var($rawTaskId, FILTER_VALIDATE_INT) : false;
if ($taskId === false || $taskId < 1) {
    header('Location: ' . app_url('tasks/index.php?error=' . urlencode('Task not found.')));
    exit;
}

$userId = current_user_id();
$load = $db->prepare(
    'SELECT id, title, description, due_date, priority, status FROM tasks WHERE id = ? AND user_id = ? LIMIT 1'
);
$load->bind_param('ii', $taskId, $userId);
$load->execute();
$task = $load->get_result()->fetch_assoc();
$load->close();

if ($task === null) {
    header('Location: ' . app_url('tasks/index.php?error=' . urlencode('Task not found.')));
    exit;
}

$page_title = 'Edit Task';
$errors = [];
$formValues = [
    'title' => $task['title'],
    'description' => $task['description'],
    'due_date' => $task['due_date'],
    'priority' => $task['priority'],
    'status' => $task['status'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formValues = [
        'title' => trim((string) ($_POST['title'] ?? '')),
        'description' => trim((string) ($_POST['description'] ?? '')),
        'due_date' => trim((string) ($_POST['due_date'] ?? '')),
        'priority' => (string) ($_POST['priority'] ?? ''),
        'status' => (string) ($_POST['status'] ?? ''),
    ];
    $errors = validate_task($formValues);

    if ($errors === []) {
        $update = $db->prepare(
            'UPDATE tasks SET title = ?, description = ?, due_date = ?, priority = ?, status = ? WHERE id = ? AND user_id = ?'
        );
        $update->bind_param(
            'sssssii',
            $formValues['title'],
            $formValues['description'],
            $formValues['due_date'],
            $formValues['priority'],
            $formValues['status'],
            $taskId,
            $userId
        );

        try {
            $update->execute();
            $update->close();
            header('Location: ' . app_url('tasks/index.php?message=' . urlencode('Task updated successfully.')));
            exit;
        } catch (mysqli_sql_exception $exception) {
            $errors['form'] = 'The task could not be updated. Please try again.';
        }

        $update->close();
    }
}

require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading">
    <div>
        <p class="eyebrow">Task CRUD</p>
        <h1>Edit Task</h1>
        <p>Update the details of your task.</p>
    </div>
</section>

<?php if (isset($errors['form'])): ?>
    <div class="alert alert--error" role="alert"><?= escape_html($errors['form']) ?></div>
<?php endif; ?>

<section class="content-card form-card">
    <form method="post" class="form-stack" novalidate>
        <input type="hidden" name="id" value="<?= escape_html($taskId) ?>">
        <div class="form-field">
            <label for="title">Task Title</label>
            <input id="title" name="title" type="text" value="<?= escape_html($formValues['title']) ?>" required>
            <?php if (isset($errors['title'])): ?><p class="field-error"><?= escape_html($errors['title']) ?></p><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5" required><?= escape_html($formValues['description']) ?></textarea>
            <?php if (isset($errors['description'])): ?><p class="field-error"><?= escape_html($errors['description']) ?></p><?php endif; ?>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label for="due_date">Due Date</label>
                <input id="due_date" name="due_date" type="date" value="<?= escape_html($formValues['due_date']) ?>" required>
                <?php if (isset($errors['due_date'])): ?><p class="field-error"><?= escape_html($errors['due_date']) ?></p><?php endif; ?>
            </div>
            <div class="form-field">
                <label for="priority">Priority</label>
                <select id="priority" name="priority" required>
                    <?php foreach (allowed_priorities() as $priority): ?>
                        <option value="<?= escape_html($priority) ?>" <?= $formValues['priority'] === $priority ? 'selected' : '' ?>><?= escape_html($priority) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['priority'])): ?><p class="field-error"><?= escape_html($errors['priority']) ?></p><?php endif; ?>
            </div>
            <div class="form-field">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <?php foreach (allowed_statuses() as $status): ?>
                        <option value="<?= escape_html($status) ?>" <?= $formValues['status'] === $status ? 'selected' : '' ?>><?= escape_html($status) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['status'])): ?><p class="field-error"><?= escape_html($errors['status']) ?></p><?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button class="button" type="submit">Save Changes</button>
            <a class="button button--ghost" href="<?= escape_html(app_url('tasks/index.php')) ?>">Cancel</a>
        </div>
    </form>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
