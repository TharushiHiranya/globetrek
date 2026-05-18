# Database

Database name: `globetrek`

Engine: MySQL. Accessed via PHP's `mysqli_*` procedural API. No ORM is used.

## Auto-setup

`includes/connection.php` connects to MySQL without selecting a database. It checks if `globetrek` exists using `SHOW DATABASES LIKE 'globetrek'`. If the database is missing, it creates the database, all four tables, and seeds demo data in one shot.

This means the app works on a fresh XAMPP install with zero manual setup. Just start MySQL and load the site.

## Schema

### Users

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR(100) | Full name |
| email | VARCHAR(100) UNIQUE | Login identifier |
| password | VARCHAR(255) | Plain text (student project) |
| role | ENUM('customer', 'staff', 'admin') | Default: customer |
| created_at | TIMESTAMP | Auto-set on insert |

### Packages

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | Primary key |
| title | VARCHAR(255) | Package name |
| destination | VARCHAR(255) | Country or city |
| description | TEXT | Full trip description |
| price | DECIMAL(10,2) | USD price |
| image_url | VARCHAR(255) | Relative path |
| created_at | TIMESTAMP | Auto-set on insert |

### Bookings

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | Primary key |
| user_id | INT | FK to users.id (CASCADE DELETE) |
| package_id | INT | FK to packages.id (CASCADE DELETE) |
| status | ENUM('pending', 'confirmed', 'cancelled') | Default: pending |
| booking_date | TIMESTAMP | Auto-set on insert |

### Queries

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | Primary key |
| user_id | INT NULL | FK to users.id (SET NULL on delete) |
| name | VARCHAR(100) | Submitter name |
| email | VARCHAR(100) | Submitter email |
| message | TEXT | Query content |
| status | ENUM('open', 'resolved') | Default: open |
| created_at | TIMESTAMP | Auto-set on insert |

## SQL files

**`db.sql`**
Schema only. Creates the database and all four tables. Run this once if you prefer manual setup over the auto-setup in `connection.php`.

**`seed.sql`**
Demo data only. Safe to re-run anytime. It wipes all four tables, resets AUTO_INCREMENT, and inserts fresh demo data. The order respects foreign key constraints.

To reset to a clean demo state:
1. Open phpMyAdmin.
2. Select the `globetrek` database.
3. Import `seed.sql`.

Alternatively use the CLI: `mysql -u root globetrek < seed.sql`

## Demo data

**Users:**
- Alice Admin: admin@globetrek.com / admin123
- Sam Staff: staff@globetrek.com / staff123
- Chris Customer: customer@globetrek.com / customer123

**Packages:** 
Five packages with images in `images/destinations/`. Includes Machu Picchu, Santorini, Costa Rica, Tokyo, and Serengeti.

**Bookings:** 
Chris Customer has two bookings. One is Machu Picchu (confirmed) and the other is Costa Rica (pending).

**Queries:** 
One from Chris Customer (logged in) and one from Jane Visitor (guest).

## Querying patterns

All queries use procedural `mysqli_*`. User input is escaped with `mysqli_real_escape_string()` before being inserted into queries. Output is escaped with `htmlspecialchars()` before being echoed to the page.

Example pattern:
```php
$email = mysqli_real_escape_string($conn, $_POST['email']);
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
echo htmlspecialchars($row['name']);
```
