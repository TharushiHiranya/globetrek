# GlobeTrek Adventures — Project Documentation

**Module:** CSE 5009 Web Application Development  
**Institution:** ICBT Campus (Cardiff Metropolitan University affiliate)  
**Assessment:** WRIT1 (100% of module)  
**Application:** GlobeTrek Adventures — Travel and Tourism Web Platform  
**Stack:** PHP 8+, MySQL, HTML5, Vanilla CSS, Minimal JavaScript  
**Server:** XAMPP (Apache + MySQL)

---

## Table of contents

1. [Project overview](#1-project-overview)
2. [Assessment requirements](#2-assessment-requirements)
3. [System architecture](#3-system-architecture)
4. [Database design](#4-database-design)
5. [Features and functionality](#5-features-and-functionality)
6. [Design system](#6-design-system)
7. [File structure](#7-file-structure)
8. [Setup and configuration](#8-setup-and-configuration)
9. [Security and error handling](#9-security-and-error-handling)
10. [Planned features](#10-planned-features)

---

## 1. Project overview

GlobeTrek Adventures is a web-based travel and tourism platform built for a newly established company based in Negombo, Sri Lanka. The platform allows customers to browse and book travel packages, staff to manage listings and bookings, and administrators to oversee all system operations.

The application is built using plain PHP and MySQL with no frameworks, making the codebase straightforward to read and extend. It runs on a standard XAMPP stack with zero manual configuration needed on a fresh install.

### Who it is for

| Role | Who they are |
|---|---|
| Customer | Members of the public who register, browse packages, and make bookings |
| Staff | GlobeTrek employees who manage packages, bookings, and customer queries |
| Admin | Senior administrators who oversee staff accounts and all system data |

### What it does

- Guests can browse all available travel packages and search by name or destination
- Customers can register, log in, book packages, cancel bookings, and send contact queries
- Staff can add new packages with images, manage all bookings, and view customer queries
- Admins have full control including managing employee accounts, featuring packages on the homepage, and removing content

---

## 2. Assessment requirements

**Module:** CSE 5009 Web Application Development  
**Assessment:** GlobeTrek Adventures WRIT1  
**Total marks:** 100% of module  
**Word count:** 3000 words  
**Module leader:** chathuriK@icbtcampus.edu.lk

### Required features status

| Feature | Status |
|---|---|
| User registration | Done |
| User login and authentication | Done |
| Browse and search travel packages | Done |
| Book a package | Done |
| Submit contact queries | Done |
| Role-based access (customer, staff, admin) | Done |
| Staff can add and manage packages | Done |
| Staff can confirm and cancel bookings | Done |
| Admin employee management | Done |
| Admin package management (feature, remove) | Done |
| Error handling throughout | Done |
| Admin sales and customer reports | Planned |
| Payment processing | Planned |
| Itinerary customization | Planned |

### Mark breakdown

**Task 01: Plan and design (40 marks)**
- 01a: Compare at least two similar travel websites (20 marks)
- 01b: UI designs and site map with design justifications (20 marks)

**Task 02: Implementation (40 marks)**
- 02a: Frontend implementation (20 marks)
- 02b: Backend implementation (20 marks)

**Task 03: Testing (20 marks)**
- Testing overview, test plan, test cases with results, user feedback, and user manual

### Learning outcomes

- LO1: Design web applications for organizational needs
- LO2: Develop server-side and client-side programs and integration
- LO3: Develop web applications with databases
- LO4: Test web applications

### Submission

- Format: PDF submitted via Turnitin on Moodle
- Filename: `[studentID] CSE5009 WRIT1`
- Late submissions are not marked without an approved extension

---

## 3. System architecture

The application follows a simple request-response model using PHP's procedural style. There are no MVC layers, routing frameworks, or templating engines. Every page is a self-contained PHP file that handles its own logic, queries the database directly, and renders the HTML response.

### Request flow

```
Browser → Apache (XAMPP) → PHP file → connection.php → MySQL (globetrek DB)
                                    ↓
                              HTML response → Browser
```

### Shared includes

Every page includes two shared files:

| File | Purpose |
|---|---|
| `includes/connection.php` | Establishes the DB connection. Auto-creates the database and tables on first load |
| `includes/header.php` | Renders the global floating glassmorphism navigation header |
| `includes/footer.php` | Renders the global footer with the CTA section |

### Session management

Sessions are started with `session_start()` at the top of each PHP file requiring authentication. After a successful login, the session stores three values:

```php
$_SESSION['user_id'] = $row['id'];
$_SESSION['role']    = $row['role'];   // 'customer', 'staff', or 'admin'
$_SESSION['name']    = $row['name'];
```

All role checks use strict string comparison (`=== 'admin'`, `=== 'staff'`, `=== 'customer'`). Pages that require a login redirect to `login.php` when the session is missing.

### JavaScript

JS is kept minimal and separated into external files in the `scripts/` folder:

| File | Purpose |
|---|---|
| `scripts/carousel.js` | Powers the featured destinations carousel on the homepage |
| `scripts/dashboard.js` | Handles the featured-package toggle checkboxes via AJAX |

---

## 4. Database design

**Database name:** `globetrek`  
**Engine:** MySQL  
**Access method:** PHP `mysqli_*` procedural API (no ORM)

### Auto-setup

`includes/connection.php` connects to MySQL without selecting a database first. It checks for the existence of `globetrek` using `SHOW DATABASES LIKE 'globetrek'`. If the database is missing, it creates the database, all four tables, and seeds demo data automatically in one transaction.

This means the application works out of the box on any fresh XAMPP installation. No manual SQL import is needed.

### Schema

#### users

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR(100) | Full name |
| email | VARCHAR(100) UNIQUE | Login identifier |
| password | VARCHAR(255) | Plain text (student project scope) |
| role | ENUM('customer', 'staff', 'admin') | Default: customer |
| created_at | TIMESTAMP | Auto-set on insert |

#### packages

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | Primary key |
| title | VARCHAR(255) | Package name |
| destination | VARCHAR(255) | Country or city |
| description | TEXT | Full trip description |
| price | DECIMAL(10,2) | Price in USD |
| image_url | VARCHAR(255) | Relative path to image |
| is_featured | TINYINT(1) | 1 = show on homepage carousel |
| created_at | TIMESTAMP | Auto-set on insert |

#### bookings

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | Primary key |
| user_id | INT | Foreign key to users.id (CASCADE DELETE) |
| package_id | INT | Foreign key to packages.id (CASCADE DELETE) |
| status | ENUM('pending', 'confirmed', 'cancelled') | Default: pending |
| booking_date | TIMESTAMP | Auto-set on insert |

#### queries

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | Primary key |
| user_id | INT NULL | Foreign key to users.id (SET NULL on delete) |
| name | VARCHAR(100) | Submitter name |
| email | VARCHAR(100) | Submitter email |
| message | TEXT | Query content |
| status | ENUM('open', 'resolved') | Default: open |
| created_at | TIMESTAMP | Auto-set on insert |

### SQL files

**`db.sql`** — Schema only. Creates the database and all four tables. Use this for manual setup if preferred over the auto-setup.

**`seed.sql`** — Demo data only. Wipes all four tables, resets AUTO_INCREMENT counters, and inserts fresh demo data. Safe to re-run at any time. Foreign key constraint order is respected.

### Demo accounts

| Name | Email | Password | Role |
|---|---|---|---|
| Alice Admin | admin@globetrek.com | admin123 | admin |
| Sam Staff | staff@globetrek.com | staff123 | staff |
| Chris Customer | customer@globetrek.com | customer123 | customer |

### Demo packages

Five packages seeded by default: Machu Picchu (Peru), Santorini Escape (Greece), Costa Rica Adventure, Tokyo Discovery (Japan), and Serengeti Safari (Tanzania). Images are stored in `images/destinations/`.

### Query patterns

All queries use procedural `mysqli_*`. User input is escaped with `mysqli_real_escape_string()` before insertion. Output is always escaped with `htmlspecialchars()` before being echoed to the page.

```php
$email  = mysqli_real_escape_string($conn, $_POST['email']);
$sql    = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $sql);
$row    = mysqli_fetch_assoc($result);
echo htmlspecialchars($row['name']);
```

---

## 5. Features and functionality

### 5.1 Guest access (no login required)

- **Homepage** (`index.php`): Hero section with a destination search bar, featured packages carousel, why-choose-us cards, and live stats counters pulling from the database.
- **Browse packages** (`packages.php`): View all available travel packages. Search by title or destination using a `LIKE` query on the `?search=` GET parameter.
- **Contact form** (`contact.php`): Submit a query to the `queries` table. Guest submissions store `user_id = NULL`.
- **Register** (`register.php`): Create a new customer account.
- **Login** (`login.php`): Authenticate and start a session.

### 5.2 Customer features

Everything a guest can do, plus:

- **Book a package**: The "Book now" button appears on package cards for logged-in customers. Clicking it calls `book.php?id=X`, which inserts a booking record with status `pending` and redirects to the dashboard.
- **View own bookings**: The customer dashboard (`dashboard.php`) shows a table of the user's bookings with package name, status, and booking date.
- **Cancel a booking**: Each booking row has a Cancel button that submits a POST request to update the booking status to `cancelled`. The button is disabled if the booking is already cancelled.

### 5.3 Staff features

Everything a customer can access on the dashboard side, plus:

- **Add new packages**: A form in the dashboard lets staff create packages with a title, destination, description, price, and optional image upload.
- **Image uploads**: Files are validated for JPG, PNG, WebP, or GIF format. The filename is sanitized to remove special characters. Files are saved to `images/destinations/`.
- **Manage all bookings**: Staff see every booking across all customers. They can change any booking's status (pending, confirmed, cancelled) via a dropdown and update button.
- **View customer queries**: A table of all contact form submissions with name, email, message, and date.

### 5.4 Admin features

Everything staff can do, plus:

- **Feature packages**: In the Existing Packages table, each row has a checkbox. Toggling it sends an AJAX request to `toggle_feature.php`, which flips the `is_featured` column. Featured packages appear in the homepage carousel.
- **Remove packages**: A Remove button (with confirmation dialog) deletes the package record and its associated image file from the server.
- **Remove queries**: A Remove button in the queries table deletes a specific query record.
- **Add employees**: A form lets admins create new staff or admin accounts by entering a name, email, password, and role. Duplicate emails are caught and rejected.
- **Remove employees**: Each employee row has a Remove button. The admin's own account is protected — the button is disabled for the currently logged-in admin.

### 5.5 Auto database setup

On the first page load of any file that includes `connection.php`, the script checks for the `globetrek` database. If it does not exist, it creates the database, all four tables, and inserts demo data without any manual intervention. This works on any fresh XAMPP installation.

### 5.6 Search

`packages.php` accepts a `?search=` GET parameter. The query uses a `LIKE` clause across both `title` and `destination` columns. The homepage hero search bar points to `packages.php?search=` so users can search directly from the landing page.

### 5.7 Redirects and access control

- Logged-in users who visit `login.php` or `register.php` are redirected to `dashboard.php`.
- `book.php` requires a logged-in customer session. Any other user is redirected to `login.php`.
- `dashboard.php` requires any valid session. Missing sessions redirect to `login.php`.
- `logout.php` calls `session_destroy()` and redirects to `login.php`.

---

## 6. Design system

GlobeTrek uses a hand-built design system with no CSS utility frameworks. All styles live in two files: `styles/style.css` (global) and `styles/home.css` (homepage only).

### Typography

Fonts are loaded from Google Fonts via `@import` in `style.css`.

| Font | Weight | Use |
|---|---|---|
| Poppins | 300, 400, 500, 600, 700 | All body text, buttons, and labels |
| Playfair Display | 400, 600, 700 | Headings only |

The combination of a serif heading font on a sans-serif body gives a travel-magazine feel.

### Color palette

| Name | Hex | Role |
|---|---|---|
| Trail Green | `#4a8b3f` | Primary brand color, main CTA buttons |
| Fern | `#427239` | Nav links, focus rings, icon accents |
| Canopy | `#3A6332` | Form card headings, price text, page titles |
| Deep Forest | `#1a2e16` | Section titles, footer headings, darkest text |
| Amber Dusk | `#e6a34d` | Secondary accent, hero "Browse Destinations" button |
| Amber Deep | `#d4892e` | Amber hover state |
| Sage Mist | `#f5f8f5` | Page background |
| Pale Fern | `#e8ede7` | Input borders, subtle card borders |
| White | `#ffffff` | Cards, forms, footer background |
| Error Red | `#c0392b` | Error messages |
| Success Green | `#27713a` | Success messages |

Trail Green is the primary brand color. Amber Dusk is used only for the highest-priority call to action on a given page.

### Button system

All buttons use the `.btn` base class plus one variant class.

| Class | Color | Use |
|---|---|---|
| `.btn-register` | Trail Green gradient | Primary actions: submit, register, book |
| `.btn-login` | White with Fern border | Secondary ghost action |
| `.btn-amber` | Amber Dusk gradient | Hero "Browse destinations" CTA |
| `.btn-outline` | Fern outline, fills on hover | Paired secondary action |
| `.btn-cta-footer` | Trail Green gradient | Footer CTA button |
| `.btn-block` | Full-width modifier | Login and register submit buttons |

A shimmer sweep (`::before` pseudo-element) is built into `.btn` and triggers on hover for all variants.

### Header

Fixed floating pill positioned 24px from the top of the viewport. It uses glassmorphism styling with a white background at reduced opacity and a `backdrop-filter: blur()`. Width is `calc(100% - 48px)` to give breathing room on each side. Nav links have a 3px underline bar that scales from center on hover using `scaleX()`.

### Footer

Light-themed card floating slightly above the page bottom with a white background and soft shadow. The footer contains:
1. `.footer-cta`: centered block with logo, headline, and CTA button
2. `.footer-main`: four-column link grid
3. `.footer-bottom`: copyright and legal links

The footer is included via `includes/footer.php` and appears on every page.

### Forms

All forms use `.form-card` as the container and `.form-group` for each field. Inputs use a Sage Mist background and switch to a Fern border with a green glow ring on focus.

### Cards

- `.package-card`: standard package listing cards in a CSS grid (`.package-grid`) that auto-fills with a 320px minimum column. Cards lift 8px on hover.
- `.featured-card`: full-image cards with an overlay gradient used in the homepage carousel. The image zooms on hover.
- `.why-us-card`: four-column icon cards. The icon box gets a Trail Green gradient on hover.

### Animations

One keyframe called `fadeInUp` exists. Hero content uses it with staggered animation delays. The `.animate-in` utility class applies it for general elements.

### Responsive breakpoints

| Breakpoint | Changes |
|---|---|
| `max-width: 1024px` | Featured grid, why-us, and stats grids become two columns |
| `max-width: 768px` | All grids become single column, header shrinks, footer stacks |
| `max-width: 480px` | Why-us and stats become single column |

---

## 7. File structure

```
web-project/
├── index.php               Homepage with hero, carousel, why-us, stats
├── login.php               Login form and session creation
├── register.php            New customer account registration
├── packages.php            Browse and search all packages
├── book.php                Creates a booking record, redirects to dashboard
├── dashboard.php           Role-based dashboard for all logged-in users
├── contact.php             Contact/query form
├── logout.php              Destroys session and redirects to login
├── toggle_feature.php      AJAX endpoint for toggling is_featured on a package
├── db.sql                  Schema only — manual setup alternative
├── seed.sql                Demo data — safe to re-run to reset state
├── favicon.ico             Site root favicon (auto-fetched by browsers)
├── includes/
│   ├── connection.php      DB connection and auto-setup logic
│   ├── header.php          Global glassmorphism navigation header
│   └── footer.php          Global footer with CTA section
├── styles/
│   ├── style.css           Global design system
│   └── home.css            Homepage-only styles
├── scripts/
│   ├── carousel.js         Featured destinations carousel logic
│   └── dashboard.js        Feature-toggle AJAX handler
├── images/
│   ├── branding/           logo.png
│   ├── destinations/       Package photos (named after destination)
│   ├── ui/                 hero-background.png
│   └── icons/              Search, globe, dollar, shield, support icons
└── docs/
    ├── documentation.md    This document — combined project documentation
    ├── test-plan.md        Full test plan and test cases
    ├── user-manual.md      Step-by-step user manual with screenshots
    └── assets/
        ├── logo.png
        ├── user-manual/    Screenshots for the user manual
        └── test-plan/      Screenshots for the test plan
```

---

## 8. Setup and configuration

### Requirements

- XAMPP (Apache + MySQL + PHP 8+)
- Any modern web browser

### Installation

1. Clone or copy the `web-project` folder into your XAMPP `htdocs` directory.
2. Start Apache and MySQL in the XAMPP control panel.
3. Open `http://127.0.0.1/web-project/` in your browser.

The database creates itself automatically on the first page load. There is nothing to import or configure.

### Resetting demo data

To return to a clean demo state:
1. Open phpMyAdmin (`http://127.0.0.1/phpmyadmin/`)
2. Select the `globetrek` database
3. Import `seed.sql`

Or via the CLI:
```bash
mysql -u root globetrek < seed.sql
```

### Image upload permissions

The `images/destinations/` folder must be writable by the Apache process. On Windows with XAMPP this is typically the default. On Linux, run:
```bash
chmod 755 images/destinations/
```

---

## 9. Security and error handling

### Input sanitization

All user input going into SQL queries is escaped using `mysqli_real_escape_string()`. All user data being echoed to the page is escaped using `htmlspecialchars()` to prevent XSS.

### Access control

- Pages requiring a session check `$_SESSION['user_id']` and redirect to `login.php` if it is absent.
- Role-specific actions check `$_SESSION['role']` with strict string comparison.
- `book.php` only allows sessions with `role === 'customer'`.
- Admin-only actions in `dashboard.php` are wrapped in `if ($role === 'admin')` checks.

### File upload security

- Uploaded files are validated against an allowed MIME type list: `image/jpeg`, `image/png`, `image/webp`, `image/gif`.
- Filenames are sanitized using `preg_replace('/[^a-zA-Z0-9._-]/', '_', ...)` before saving.

### Error handling

| Scenario | Behavior |
|---|---|
| Database connection failure | Script exits with the connection error message |
| Login with wrong password | Inline error message shown above the form |
| Registration with duplicate email | MySQL error caught and shown to the user |
| File upload failure | Error message shown in the dashboard |
| Missing session | Redirect to `login.php` |
| Admin tries to remove themselves | Action blocked with an error message |

### Note on passwords

Passwords are stored as plain text in this project. This is intentional for the scope of a student assignment. In a production application, passwords should be hashed using `password_hash()` and verified using `password_verify()`.

---

## 10. Planned features

These features are required by the assessment brief and are intended for future iterations:

| Feature | Description |
|---|---|
| Payment integration | Allow customers to pay for bookings online |
| Admin sales reports | Revenue and booking statistics for admins |
| Email notifications | Booking confirmation emails to customers |
| Package customization | Custom itinerary building per trip |
| Accommodation details | Per-package hotel and transport information |
| Admin user management | Full CRUD for customer accounts |

When adding any of these features, follow the existing patterns: include `connection.php` at the top, start sessions with `session_start()`, use `htmlspecialchars()` on all output, and use `mysqli_real_escape_string()` on all input going into queries.
