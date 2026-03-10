# JMedi – Smart Medical Platform

A full-featured SaaS-style medical management system built with **PHP 8.2** and **MySQL**. Provides a patient-facing public portal and a powerful admin/doctor backend for managing appointments, schedules, departments, doctors, and all site content.

**Live Demo:** [https://jmedi.jnvweb.in](https://jmedi.jnvweb.in)

---

## Features

### Public Portal
- Homepage with dynamic hero sliders, department showcase, doctor listings, and testimonials
- Doctor profiles — two distinct layout templates (Classic Dark, Modern Light)
- Clean SEO-friendly URLs: `/doctor/dr-james-wilson`, `/departments`, `/blog`
- Multi-step appointment booking with real-time slot availability
- Patient login, registration, and personal dashboard
- Blog, contact page, and dynamic CMS-managed pages

### Admin Panel
- Full CRUD for doctors, departments, patients, blog posts, testimonials, and hero slides
- Appointment workflow: Pending → Confirmed → Completed / Cancelled / Rescheduled
- WhatsApp and email quick-action links per appointment
- Doctor schedule builder (per day, per session, with configurable slot duration)
- Role-based access: Super Admin, Admin, Doctor
- Site settings, menu manager, homepage section editor (CKEditor)
- Toast popup notifications on all save/update actions
- Database and full-site backup/download tools

### Doctor Profile Templates
| Template | Style | Description |
|---|---|---|
| **Template 1 — Classic** | Dark blue gradient hero | Professional dark header, tabbed layout for schedule, reviews, qualifications |
| **Template 2 — Modern** | Light blue/white hero | Full-width photo, floating stat cards (Experience, Success Rate), animated badges |

Select template per doctor in Admin → Doctors → Edit Doctor → "Profile Template" dropdown.

---

## Technology Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2 |
| Database | MySQL 5.7+ / MariaDB 10.3+ |
| Frontend | Bootstrap 5.3, Swiper.js, Chart.js, CKEditor |
| Web Server | Apache 2.4 with `mod_rewrite` (cPanel compatible) |
| Dev Server | PHP Built-in Server via `router.php` |

---

## Requirements

| Requirement | Version |
|---|---|
| PHP | 8.2 or higher |
| MySQL / MariaDB | 5.7+ / 10.3+ |
| PHP Extensions | `pdo_mysql`, `fileinfo`, `zip`, `mbstring`, `json`, `gd` |
| Apache | 2.4+ with `mod_rewrite` enabled |

---

## cPanel Setup Guide

### Step 1 — Connect GitHub to cPanel

1. In cPanel, go to **Git Version Control**.
2. Click **Create** and paste the repository URL:
   ```
   https://github.com/JNVWEBin/jmedi-smart-medical-platform
   ```
3. Set the clone path to your domain's root folder (e.g., `public_html` or the subdomain folder).
4. Click **Create** to clone the repository.

Your server structure should look like:
```
public_html/
├── .htaccess
├── router.php
├── admin/
├── assets/
├── database/
├── includes/
└── public/
```

---

### Step 2 — Create a MySQL Database

1. In cPanel → **MySQL Databases**:
   - Create a new database, e.g. `youruser_jmedi`
   - Create a database user with a strong password
   - Grant the user **All Privileges** on the database
2. Note down: **hostname** (usually `localhost`), **database name**, **username**, **password**

---

### Step 3 — Configure Database Connection

Open `includes/db.php` and update the credentials:

```php
$host   = 'localhost';
$dbname = 'youruser_jmedi';
$user   = 'youruser_jmedi';
$pass   = 'your_strong_password';
```

Alternatively, set a `DATABASE_URL` environment variable in cPanel:
```
mysql://username:password@localhost/database_name
```

---

### Step 4 — Import the Database

1. Open **phpMyAdmin** from cPanel.
2. Select your database from the left panel.
3. Click the **Import** tab.
4. Choose the file `database/jmedi_setup.sql` from your project folder.
5. Click **Go**.

This single file:
- Creates all tables (safe for both fresh and existing databases — uses `CREATE IF NOT EXISTS`)
- Adds all required columns (`ALTER TABLE ... ADD COLUMN IF NOT EXISTS`)
- Inserts default data: departments, sample doctors, blog posts, menus, settings
- Fixes any double `dr-dr-` prefix slugs (`UPDATE IGNORE`)

---

### Step 5 — Set Folder Permissions

In **File Manager** or via SSH:

| Path | Permission |
|---|---|
| `assets/uploads/` | `755` |
| `assets/logos/` | `755` |
| `backups/` | `755` |
| All `.php` files | `644` |
| All directories | `755` |

---

### Step 6 — Configure PHP Version

1. In cPanel → **MultiPHP Manager** or **Select PHP Version**
2. Set PHP version to **8.2**
3. Enable extensions: `pdo_mysql`, `fileinfo`, `zip`, `mbstring`, `json`, `gd`

---

### Step 7 — Verify Installation

Visit your domain — you should see the JMedi homepage with hero slider.

**Admin Panel:**
```
https://yourdomain.com/admin/
```

| Field | Default Value |
|---|---|
| Username | `admin` |
| Password | `password` |

> Change the default password immediately via Admin → Profile.

---

## URL Structure (Clean URLs)

The `.htaccess` file handles all clean URL routing on Apache/cPanel:

| Clean URL | Maps To |
|---|---|
| `/` | `public/index.php` |
| `/doctors` | `public/doctors.php` |
| `/departments` | `public/departments.php` |
| `/blog` | `public/blog.php` |
| `/contact` | `public/contact.php` |
| `/appointment` | `public/appointment.php` |
| `/doctor/dr-james-wilson` | `public/doctor-profile.php?slug=dr-james-wilson` |
| `/admin/` | `admin/index.php` (redirects to login if not authenticated) |

Doctor slugs are auto-generated from the doctor's name on save, with `dr-` prefix (e.g., `Dr. James Wilson` → `dr-james-wilson`).

---

## Directory Structure

```
jmedi/
├── .htaccess                   # Apache URL routing & security rules
├── router.php                  # PHP built-in dev server router
├── admin/
│   ├── login.php               # Admin login
│   ├── dashboard.php           # Overview stats & charts
│   ├── doctors.php             # Doctor management (CRUD + template select)
│   ├── appointments.php        # Appointment workflow
│   ├── departments.php         # Department management
│   ├── blog.php                # Blog post management
│   ├── hero-sliders.php        # Homepage hero slide management
│   ├── home-sections.php       # Homepage section content editor
│   ├── testimonials.php        # Patient testimonials
│   ├── settings.php            # Site-wide settings (name, logo, contact)
│   ├── menu-manager.php        # Navigation menu builder
│   ├── users.php               # Admin user management
│   ├── profile.php             # Admin/doctor own profile
│   └── backup.php              # Database & file backup tools
├── assets/
│   ├── css/
│   │   ├── style.css           # Public frontend styles
│   │   ├── admin.css           # Admin panel styles
│   │   └── hero-slider.css     # Hero slider animations
│   ├── js/
│   │   ├── main.js             # Frontend JavaScript
│   │   └── hero-slider.js      # Hero slider logic
│   ├── uploads/                # Uploaded images (doctor photos, blog, sections)
│   └── logos/                  # Site logo uploads
├── database/
│   └── jmedi_setup.sql         # Complete MySQL import file — single file for all installs
├── includes/
│   ├── db.php                  # MySQL PDO connection
│   ├── auth.php                # Session handling, CSRF tokens, RBAC
│   ├── functions.php           # Shared helpers (slug generation, etc.)
│   ├── header.php              # Public site header/nav
│   ├── footer.php              # Public site footer
│   ├── admin_header.php        # Admin panel header/sidebar
│   └── admin_footer.php        # Admin panel footer + toast notifications
└── public/
    ├── index.php               # Homepage
    ├── doctors.php             # Doctors listing with search/filter
    ├── doctor-profile.php      # Doctor profile (Template 1 or 2)
    ├── departments.php         # Departments listing + detail
    ├── blog.php                # Blog listing + post view
    ├── appointment.php         # Appointment booking (multi-step)
    ├── contact.php             # Contact form
    ├── patient-login.php       # Patient login/register
    ├── patient-dashboard.php   # Patient appointments dashboard
    └── api/
        ├── appointment.php     # Submit appointment (JSON)
        ├── doctors-by-dept.php # Get doctors by department (JSON)
        ├── slots.php           # Available slots by doctor+date (JSON)
        └── login.php           # Admin login API (JSON)
```

---

## User Roles

| Role | Access |
|---|---|
| **Super Admin** | Full access — all settings, user management, backups, everything |
| **Admin** | Configurable per-feature permissions (doctors, appointments, blog, CMS, etc.) |
| **Doctor** | Own profile, own appointment list, own schedule only |

---

## Troubleshooting

**Doctor profile URL shows homepage instead of profile**
- Confirm the `.htaccess` has been pulled from GitHub to the live server.
- Check Apache has `mod_rewrite` enabled (`AllowOverride All` in virtual host config).
- The doctor record must have a valid `slug` in the database.

**Blank page or 500 error on any page**
- In cPanel MultiPHP Manager → confirm PHP 8.2 is selected.
- Temporarily enable error display: add `php_flag display_errors On` to `.htaccess`.
- Check all required PHP extensions are enabled (`pdo_mysql`, `fileinfo`, `zip`).

**Database connection failed**
- Verify credentials in `includes/db.php`.
- On cPanel, the hostname is almost always `localhost`.
- Confirm the database user has all privileges on the database.

**Doctor slug shows `dr-dr-name` (double prefix)**
- Run this SQL in phpMyAdmin:
  ```sql
  UPDATE IGNORE doctors SET slug = CONCAT('dr-', SUBSTRING(slug, 4))
  WHERE slug LIKE 'dr-dr-%';
  ```

**Doctor update/save fails with database error**
- Run `database/jmedi_setup.sql` through phpMyAdmin Import — this safely adds any missing columns.

**Clean URLs like `/departments` go to homepage**
- Pull the latest `.htaccess` from GitHub (explicit route rules added).
- Confirm `.htaccess` is in the root (same folder as `router.php`), not inside `public/`.

**Images not uploading**
- Check `assets/uploads/` and `assets/logos/` exist with `755` permissions.
- Confirm `fileinfo` PHP extension is enabled in PHP settings.

---

## Security

- All form submissions protected with CSRF tokens
- Passwords hashed with `password_hash()` (bcrypt, cost 10)
- All DB queries use PDO prepared statements (no SQL injection)
- File uploads validated against real MIME type (not browser-supplied)
- Doctor accounts can only access own data (IDOR protection)
- Session cookies: `httponly`, `samesite=Strict`
- Directory listing disabled via `Options -Indexes`
- Sensitive file types (`.env`, `.sql`, `.sh`, `.bak`) blocked via `.htaccess`

---

## Local Development

```bash
# Requires PHP 8.2 and a MySQL database

# Set your DB credentials in includes/db.php then run:
php -S 0.0.0.0:5000 router.php
```

Visit `http://localhost:5000`. The `router.php` handles clean URL routing locally (replaces `.htaccess` for the PHP built-in server).

---

## Updating from GitHub (cPanel)

After pulling new code in cPanel → Git Version Control:

1. If database changes were made, re-run `database/jmedi_setup.sql` in phpMyAdmin (safe to run multiple times — uses `IF NOT EXISTS` and `IGNORE`).
2. No server restart needed — PHP is stateless.

---

## License

Proprietary software developed by **JNVWeb**. All rights reserved.

---

*Built with PHP 8.2 · MySQL · Bootstrap 5.3 · Swiper.js · Chart.js · CKEditor · Font Awesome 6*
