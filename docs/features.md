# Features

What the application currently does, organized by user role.

## Guest

- View the homepage with featured packages, why-us section, and stats counter.
- Browse all packages on `/packages.php`. Search by name or destination.
- Submit a contact query on `/contact.php`. This saves to `queries` table with `user_id = NULL`.
- Register a new account on `/register.php`.
- Log in on `/login.php`.

## Customer

Everything a guest can do, plus:
- View their own bookings in the dashboard.
- Book a package via `/book.php`. This creates a booking record with status 'pending'.

## Staff

Everything a customer can access from the dashboard, plus:
- Add new packages through the dashboard form.
- Upload images to `images/destinations/`. Uploads are validated for JPG, PNG, WebP, or GIF formats.
- View all bookings across all customers.
- Change booking status from the dashboard.
- View all contact queries submitted through the site.

## Admin

Everything staff can do, plus:
- View the list of employees.
- Add new employees and assign roles.
- Remove employees and packages.
- Admin user management and reports are planned.

## Auto database setup

On first load of any page, `includes/connection.php` checks if the `globetrek` database exists. If not, it creates the database, creates all four tables, and inserts demo data automatically. No manual database setup is needed.

## Session management

Sessions are started with `session_start()` at the top of each PHP file that needs authentication. After login, `$_SESSION` stores `user_id`, `role`, and `name`. Pages that require authentication redirect to `login.php` if the session is missing. The `logout.php` file destroys the session and redirects.

## Search

The `packages.php` file accepts a `?search=` GET parameter. The query searches both `title` and `destination` fields using a SQL `LIKE` clause. The search bar on the homepage hero points to `packages.php?search=`.

## Image uploads

The dashboard add-package form uses `enctype="multipart/form-data"`. On submit, PHP validates the file, sanitizes the filename, and moves it to `images/destinations/`. The relative path is stored in the `image_url` column.

## Error handling

- Database connection failure causes a script exit with the connection error message.
- Login failure shows an inline error message above the form.
- Registration with a duplicate email catches the MySQL error and shows it to the user.
- File upload failure shows an error message in the dashboard.
- Pages redirect to `login.php` if a session is missing.

## Planned features

These are required by the assessment brief and should be added in future iterations:
- Payment integration.
- Admin user management.
- Sales reports.
- Email notifications.
- Package customization.
- Accommodation and transport details.
