<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/validation.php';

require_login();

$userId = current_user_id();
$search = trim((string) ($_GET['search'] ?? ''));
$statusFilter = (string) ($_GET['status'] ?? '');
$priorityFilter = (string) ($_GET['priority'] ?? '');

if (!in_array($statusFilter, allowed_statuses(), true)) {
    $statusFilter = '';
}

if (!in_array($priorityFilter, allowed_priorities(), true)) {
    $priorityFilter = '';
}

$likeSearch = '%' . $search . '%';
$taskStatement = $db->prepare(
    "SELECT id, title, description, due_date, priority, status
     FROM tasks
     WHERE user_id = ?
       AND title LIKE ?
       AND (? = '' OR status = ?)
       AND (? = '' OR priority = ?)
     ORDER BY due_date ASC, id DESC"
);
$taskStatement->bind_param(
    'isssss',
    $userId,
    $likeSearch,
    $statusFilter,
    $statusFilter,
    $priorityFilter,
    $priorityFilter
);
$taskStatement->execute();
$tasks = $taskStatement->get_result()->fetch_all(MYSQLI_ASSOC);
$taskStatement->close();

$page_title = 'My Tasks';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading">
    <div>
        <p class="eyebrow">Your list</p>
        <h1>My Tasks</h1>
        <p>Only tasks belonging to your account appear here.</p>
    </div>
    <a class="button" href="<?= escape_html(app_url('tasks/create.php')) ?>">Add New Task</a>
</section>

<?php if (isset($_GET['message'])): ?>
    <div class="alert alert--success" role="status"><?= escape_html($_GET['message']) ?></div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="alert alert--error" role="alert"><?= escape_html($_GET['error']) ?></div>
<?php endif; ?>

<section class="content-card">
    <form method="get" class="filter-form">
        <div class="form-field filter-form__search">
            <label for="search">Search Task</label>
            <input id="search" name="search" type="search" value="<?= escape_html($search) ?>" placeholder="Search by title">
        </div>
        <div class="form-field">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="">All</option>
                <?php foreach (allowed_statuses() as $status): ?>
                    <option value="<?= escape_html($status) ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= escape_html($status) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label for="priority">Priority</label>
            <select id="priority" name="priority">
                <option value="">All</option>
                <?php foreach (allowed_priorities() as $priority): ?>
                    <option value="<?= escape_html($priority) ?>" <?= $priorityFilter === $priority ? 'selected' : '' ?>><?= escape_html($priority) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="button" type="submit">Search</button>
        <a class="button button--ghost" href="<?= escape_html(app_url('tasks/index.php')) ?>">Clear</a>
    </form>
</section>

<section class="content-card">
    <div class="section-heading section-heading--row">
        <div>
            <p class="eyebrow">Your work</p>
            <h2>Task List</h2>
        </div>
        <span class="result-count"><?= escape_html(count($tasks)) ?> task<?= count($tasks) === 1 ? '' : 's' ?></span>
    </div>

    <?php if ($tasks === []): ?>
        <div class="empty-state">
            <h3>No tasks found</h3>
            <p>Try a different search or add a new task.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th scope="col">Task</th>
                    <th scope="col">Due Date</th>
                    <th scope="col">Priority</th>
                    <th scope="col">Status</th>
                    <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td>
                            <strong><?= escape_html($task['title']) ?></strong>
                            <span class="task-description"><?= escape_html($task['description']) ?></span>
                        </td>
                        <td><?= escape_html(date('M j, Y', strtotime($task['due_date']))) ?></td>
                        <td><span class="badge badge--<?= escape_html(strtolower($task['priority'])) ?>"><?= escape_html($task['priority']) ?></span></td>
                        <td><span class="badge badge--<?= escape_html(strtolower($task['status'])) ?>"><?= escape_html($task['status']) ?></span></td>
                        <td>
                            <div class="action-list">
                                <a href="<?= escape_html(app_url('tasks/edit.php?id=' . $task['id'])) ?>">Edit</a>
                                <form method="post" action="<?= escape_html(app_url('tasks/delete.php')) ?>" data-confirm-delete>
                                    <input type="hidden" name="id" value="<?= escape_html($task['id']) ?>">
                                    <button class="link-button link-button--danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
