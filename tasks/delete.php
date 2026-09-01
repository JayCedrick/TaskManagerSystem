<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

$taskListUrl = app_url('tasks/index.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $taskListUrl . '?error=' . urlencode('Invalid delete request.'));
    exit;
}

$rawTaskId = $_POST['id'] ?? '';
$taskId = is_scalar($rawTaskId) ? filter_var($rawTaskId, FILTER_VALIDATE_INT) : false;
if ($taskId === false || $taskId < 1) {
    header('Location: ' . $taskListUrl . '?error=' . urlencode('Task not found.'));
    exit;
}

$userId = current_user_id();
$delete = $db->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
$delete->bind_param('ii', $taskId, $userId);
$delete->execute();
$wasDeleted = $delete->affected_rows > 0;
$delete->close();

$messageKey = $wasDeleted ? 'message' : 'error';
$message = $wasDeleted ? 'Task deleted successfully.' : 'Task not found.';
header('Location: ' . $taskListUrl . '?' . $messageKey . '=' . urlencode($message));
exit;
