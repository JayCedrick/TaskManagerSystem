# Student Task Management System Presentation Script

## Opening

Good day. For this project, I created a Student Task Management System with
authentication. Basically, the purpose of the system is to let students create,
organize, and track their own school tasks in one place.

I used Vanilla PHP, MySQL, HTML5, CSS3, and JavaScript. I kept the system
simple because the main goal was to demonstrate the basic flow of a web
application: the user registers, logs in, manages data, and logs out securely.

## Registration and authentication

First, a new user can register using their full name, username, email, password,
and password confirmation. The form checks that the required fields are filled
in, that the email format is valid, that the username and email are not already
being used, and that both passwords match.

For security, I do not save the actual password in the database. PHP converts it
into a secure password hash using `password_hash()`. So even if somebody looks
at the password column, they will not see the original password.

When the user logs in, they can use either their username or their email. The
system looks for the account and uses `password_verify()` to compare the entered
password with the saved hash. If the credentials are correct, PHP creates a
session containing the user's ID and name, then redirects the user to the
dashboard.

The session is important because it lets the application remember who is
currently logged in. Protected pages check this session before showing anything.
If somebody tries to open the dashboard or task pages without logging in, the
system redirects them back to the login page.

## Dashboard

After logging in, the user sees the dashboard. It welcomes the user by name and
shows the total number of tasks, the number of pending tasks, and the number of
completed tasks.

These counts are filtered using the current user's ID. So the dashboard only
shows the summary for that account. The user can also go to My Tasks or add a
new task from here.

## How tasks work

Each task has a title, description, due date, priority, status, and creation
date. The priorities are Low, Medium, and High. The statuses are Pending and
Completed.

When I create a task, the system automatically connects it to the ID of the
currently logged-in user. I do not allow the user to choose another user ID in
the form. This makes sure the task belongs to the correct account.

On the My Tasks page, the user can see the task title, due date, priority,
status, and available actions. The user can edit the task details, including
the title, description, due date, priority, and status. The user can also delete
a task, but the system first asks for confirmation with the message:

“Are you sure you want to delete this task?”

There is also a search field for finding tasks by title. Moreover, the user can
filter the list by status or priority. This makes the list more useful when the
user has several school tasks.

## Database structure

The database is called `student_task_manager`. It has two main tables.

The `users` table stores the user ID, full name, username, email, hashed
password, and account creation date.

The `tasks` table stores the task ID, the related `user_id`, title, description,
due date, priority, status, and creation date. The `user_id` connects each task
to its owner through a foreign key.

So the relationship is simple: one user can have many tasks, but every task
belongs to one user.

## Security and user ownership

I used prepared statements when sending user input to MySQL. This helps protect
the system from SQL injection. I also escape values before displaying them in
HTML.

The most important ownership rule is that task queries include both the task ID
and the current user's ID. On the other hand, knowing another task's ID is not
enough to edit or delete it. If a different user tries to access that task, the
system treats it as not found.

I tested this by creating a second account and trying to access the first
account's task. The second account could not view, edit, or delete that task.

When the user logs out, the system clears the session, removes the login state,
and redirects to the login page. This prevents the account from staying
authenticated after logout.

## Demonstration flow

For the demonstration, I will first open a protected page while logged out to
show that it redirects to the login page. Then I will register an account and
show the validation messages for missing or invalid information.

After that, I will log in using the username and show the dashboard counts. I
will create a task, edit its details, change its status, and show how the counts
change. I will also use the title search, status filter, and priority filter.

Finally, I will demonstrate the delete confirmation, log out, and create or use
a second account to show that users can only manage their own tasks.

## Closing

In summary, this system demonstrates a complete basic task management flow with
registration, login, session authentication, a user-specific dashboard, task
CRUD operations, searching, filtering, and logout.

The main thing I focused on was keeping the system understandable while still
handling the important security requirements. So the application is small, but
the core flow from authentication to task ownership is complete and working.
