# 🌍 GlobeTrek Adventures

A travel and tourism web app built with PHP and MySQL. Customers can browse and book packages, staff manage the listings and bookings, and admins oversee everything. Built as a university project for CSE 5009 Web Application Development at ICBT Campus.

---

## What it does

- Browse and search travel packages by name or destination
- Register, log in, and manage bookings from a personal dashboard
- Staff can add packages (with image upload) and confirm or cancel bookings
- Contact form for customer queries (works for guests too)
- Automatic database setup on first run, no manual SQL needed

---

## Getting started

You need XAMPP (or any Apache + MySQL + PHP 8+ setup).

1. Clone or copy this folder into `htdocs/web-project/`
2. Start Apache and MySQL in XAMPP
3. Open `http://localhost/web-project/` in your browser

The database creates itself on the first load. There is nothing to import or configure.

---

## Demo accounts

| Role | Email | Password |
|---|---|---|
| Admin | admin@globetrek.com | admin123 |
| Staff | staff@globetrek.com | staff123 |
| Customer | customer@globetrek.com | customer123 |

---

## Stack

- **Backend:** PHP 8+ (procedural, no framework)
- **Database:** MySQL via `mysqli_*`
- **Frontend:** HTML5, vanilla CSS, minimal JS
- **Fonts:** Poppins + Playfair Display (Google Fonts)
- **Server:** XAMPP

No Tailwind. No Bootstrap. No jQuery.

---

## Project structure

```
web-project/
├── index.php        Homepage
├── packages.php     Browse and search packages
├── book.php         Book a package
├── dashboard.php    Role-based dashboard
├── contact.php      Contact form
├── login.php
├── register.php
├── logout.php
├── db.sql           Schema (manual setup alternative)
├── seed.sql         Reset demo data anytime
├── includes/
│   ├── connection.php   Auto-creates DB on first run
│   ├── header.php
│   └── footer.php
├── styles/
│   ├── style.css    Global design system
│   └── home.css     Homepage styles
├── images/
│   ├── branding/    Logo
│   ├── destinations/ Package photos
│   ├── ui/          Hero background
│   └── icons/
└── docs/            Full project documentation
```

---

## Resetting demo data

If you want a fresh start, run `seed.sql` in phpMyAdmin (select the `globetrek` database, then import the file). Or via the CLI:

```bash
mysql -u root globetrek < seed.sql
```

---

## Docs

The `docs/` folder has the full picture:

- [`docs/design.md`](docs/design.md) — color system, fonts, components
- [`docs/database.md`](docs/database.md) — schema and seeding
- [`docs/features.md`](docs/features.md) — what's built, what's planned
- [`docs/requirements.md`](docs/requirements.md) — assessment brief and grading notes

---

## Notes

Passwords are stored as plain text. This is intentional for the scope of this student project. Don't use this in production.
