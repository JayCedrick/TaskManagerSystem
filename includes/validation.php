<?php

function allowed_priorities(): array
{
    return ['Low', 'Medium', 'High'];
}

function allowed_statuses(): array
{
    return ['Pending', 'Completed'];
}

function validate_registration(array $input): array
{
    $errors = [];
    $fullName = trim((string) ($input['full_name'] ?? ''));
    $username = trim((string) ($input['username'] ?? ''));
    $email = trim((string) ($input['email'] ?? ''));
    $password = (string) ($input['password'] ?? '');
    $confirmPassword = (string) ($input['confirm_password'] ?? '');

    if ($fullName === '') {
        $errors['full_name'] = 'Full name is required.';
    }

    if ($username === '') {
        $errors['username'] = 'Username is required.';
    }

    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Enter a valid email address.';
    }

    if ($password === '') {
        $errors['password'] = 'Password is required.';
    }

    if ($confirmPassword === '') {
        $errors['confirm_password'] = 'Please confirm your password.';
    } elseif ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    return $errors;
}

function validate_task(array $input): array
{
    $errors = [];
    $title = trim((string) ($input['title'] ?? ''));
    $description = trim((string) ($input['description'] ?? ''));
    $dueDate = trim((string) ($input['due_date'] ?? ''));
    $priority = (string) ($input['priority'] ?? '');
    $status = (string) ($input['status'] ?? '');

    if ($title === '') {
        $errors['title'] = 'Task title is required.';
    }

    if ($description === '') {
        $errors['description'] = 'Description is required.';
    }

    if ($dueDate === '') {
        $errors['due_date'] = 'Due date is required.';
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $dueDate);
        $dateErrors = DateTime::getLastErrors();
        $hasDateErrors = is_array($dateErrors)
            && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0);

        if ($date === false || $hasDateErrors || $date->format('Y-m-d') !== $dueDate) {
            $errors['due_date'] = 'Enter a valid due date.';
        }
    }

    if (!in_array($priority, allowed_priorities(), true)) {
        $errors['priority'] = 'Select a valid priority.';
    }

    if (!in_array($status, allowed_statuses(), true)) {
        $errors['status'] = 'Select a valid status.';
    }

    return $errors;
}
