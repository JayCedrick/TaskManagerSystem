# Student Task Manager

A small database-driven student task management system built for Week 9 TLA 1.
It uses Vanilla PHP, MySQL, HTML5, CSS3, and JavaScript.

## Requirements

- XAMPP or WAMP with PHP and MySQL/MariaDB.
- PHP 8.0 or newer.
- The PHP `mysqli` extension enabled.

## XAMPP/WAMP setup

1. Copy the `student-task-manager` folder into the web root:
   - XAMPP: `htdocs/student-task-manager`
   - WAMP: `www/student-task-manager`
2. Start Apache and MySQL from the control panel.
3. Import `database.sql` using phpMyAdmin or the MySQL client. The script
   creates the `student_task_manager` database and both required tables.
4. If the local MySQL account is not the XAMPP default, set the connection
   values with environment variables or edit the defaults in
   `config/database.php`:

   ```text
   DB_HOST=localhost
   DB_USER=root
   DB_PASSWORD=
   DB_NAME=student_task_manager
   ```

5. Open `http://localhost/student-task-manager/` in a browser.

For a local PHP-only check from the workspace root, run `php -S` with the
workspace root as the document root and open the same application path:

```bash
php -S 127.0.0.1:8080 -t .
```

Then open `http://127.0.0.1:8080/student-task-manager/`.

## Project structure

```text
student-task-manager/
├── config/database.php
├── includes/auth.php
├── includes/footer.php
├── includes/header.php
├── includes/validation.php
├── css/style.css
├── js/script.js
├── tasks/index.php
├── tasks/create.php
├── tasks/edit.php
├── tasks/delete.php
├── dashboard.php
├── index.php
├── login.php
├── logout.php
├── register.php
└── database.sql
```

## Security and data rules

- Passwords are stored with `password_hash($password, PASSWORD_DEFAULT)`.
- Login uses `password_verify($password, $user['password'])`.
- User input is sent through MySQLi prepared statements.
- Every task query includes the current logged-in user's id.
- Output is escaped before it is written into HTML.
- Logout clears the session and destroys the authentication state.

## Local checks

Run the deterministic checks from the workspace root:

```bash
php student-task-manager/tests/validation_test.php
php student-task-manager/tests/auth_test.php
php student-task-manager/tests/ownership_source_test.php
php student-task-manager/tests/assets_test.php
find student-task-manager -name '*.php' -print0 | xargs -0 -n1 php -l
```

## Demonstration checklist

1. Open a protected page while logged out and show the redirect to `login.php`.
2. Register a user with all five fields. Demonstrate required-field,
   invalid-email, mismatched-password, duplicate-username, and duplicate-email
   messages.
3. Log in using the username, then log out and log in using the email.
4. Show the dashboard welcome name and total, pending, and completed counts.
5. Add a task, then edit its title, description, due date, priority, and status.
6. Use title search, status filtering, and priority filtering on My Tasks.
7. Use Delete and show `Are you sure you want to delete this task?` before
   confirming the deletion.
8. Create a second account and demonstrate that it cannot view, edit, or delete
   the first account's task by changing the task id.
9. Log out and show that protected pages redirect to `login.php` again.
