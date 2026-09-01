<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

$userId = current_user_id();
$summaryStatement = $db->prepare(
    "SELECT
        COUNT(*) AS total_tasks,
        COALESCE(SUM(status = 'Pending'), 0) AS pending_tasks,
        COALESCE(SUM(status = 'Completed'), 0) AS completed_tasks
     FROM tasks
     WHERE user_id = ?"
);
$summaryStatement->bind_param('i', $userId);
$summaryStatement->execute();
$summary = $summaryStatement->get_result()->fetch_assoc();
$summaryStatement->close();

$page_title = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading page-heading--dashboard">
    <div>
        <p class="eyebrow">Your workspace</p>
        <h1>Welcome, <?= escape_html($_SESSION['full_name'] ?? 'Student') ?>!</h1>
        <p>Stay on top of your school work with a simple task list.</p>
    </div>
    <a class="button" href="<?= escape_html(app_url('tasks/create.php')) ?>">Add New Task</a>
</section>

<section class="summary-grid" aria-label="Task summary">
    <article class="summary-card">
        <span class="summary-card__label">Total Tasks</span>
        <strong><?= escape_html($summary['total_tasks'] ?? 0) ?></strong>
    </article>
    <article class="summary-card summary-card--pending">
        <span class="summary-card__label">Pending</span>
        <strong><?= escape_html($summary['pending_tasks'] ?? 0) ?></strong>
    </article>
    <article class="summary-card summary-card--completed">
        <span class="summary-card__label">Completed</span>
        <strong><?= escape_html($summary['completed_tasks'] ?? 0) ?></strong>
    </article>
</section>

<section class="content-card dashboard-card">
    <div class="section-heading section-heading--row">
        <div>
            <p class="eyebrow">Keep moving</p>
            <h2>My Tasks</h2>
        </div>
        <a class="button button--secondary" href="<?= escape_html(app_url('tasks/index.php')) ?>">View All Tasks</a>
    </div>
    <p>Review, search, edit, or complete your personal tasks from the task list.</p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
