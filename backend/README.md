<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Project Overview (School Attendance Backend)

- **Purpose**: Backend API (Laravel 10) for managing students and their daily attendance records in a school.
- **Key features**:
  - Student management (CRUD with class, section, unique student_id, and optional photo).
  - Attendance tracking per day and per student (status: present/absent/late, note, recorded_by user).
  - Monthly attendance reports per student with optional class filter.
  - Today’s attendance statistics (present/absent/late counts and percentage).
  - Sanctum-based API authentication (`auth:sanctum`) protecting student and attendance routes.
- **Tech stack**: PHP 8.1+, Laravel 10, MySQL, Laravel Sanctum, PHPUnit.

## Setup & Installation

1. **Clone and install dependencies**
   ```bash
   git clone <your-repo-url>
   cd backend
   composer install
   ```
2. **Environment configuration**
   - Copy `.env.example` to `.env` and generate an app key:
     ```bash
     cp .env.example .env
     php artisan key:generate
     ```
   - Configure your main MySQL database in `.env`:
     - `DB_DATABASE=school_attendance`
     - `DB_USERNAME=your_mysql_user`
     - `DB_PASSWORD=your_mysql_password`
3. **Database migrations and seeders**
   - Create the main application database in MySQL.
   - Run migrations (and seeders if desired):
     ```bash
     php artisan migrate --seed
     ```
4. **Run the development server**
   ```bash
   php artisan serve
   ```

## API Overview

- **Base URL**: `/api/v1`

- **Authentication**
  - `POST /api/v1/register` – Register a new user and return an API token.
  - `POST /api/v1/login` – Login and receive an API token.
  - `POST /api/v1/logout` – Logout (revoke current token); requires `auth:sanctum`.

- **Student endpoints** (protected by `auth:sanctum`)
  - `GET /api/v1/students` – List students.
    - Query params: `class`, `section`, `search`, `per_page`.
  - `POST /api/v1/students` – Create a student.
  - `GET /api/v1/students/{student}` – Show a student.
  - `PUT /api/v1/students/{student}` – Update a student.
  - `DELETE /api/v1/students/{student}` – Delete a student.

- **Attendance endpoints** (protected by `auth:sanctum`, `attendances` prefix)
  - `GET /api/v1/attendances` – List attendance records.
    - Query params: `date` (`Y-m-d`), `student_id`, `status`, `per_page`.
  - `POST /api/v1/attendances` – Record **bulk** attendance for multiple students in one request.
  - `GET /api/v1/attendances/{attendance}` – Show a single attendance record.
  - `GET /api/v1/attendances/reports/monthly` – Monthly report.
    - Query params: `month=YYYY-MM` (required), `class` (optional).
  - `GET /api/v1/attendances/statistics/today` – Today’s statistics summary.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Testing (Project-specific)

- **In-memory SQLite for tests**
  - PHPUnit is configured (via `phpunit.xml`) to use an in-memory SQLite database for tests:
    - `DB_CONNECTION=sqlite`
    - `DB_DATABASE=:memory:`
  - This keeps your main MySQL data untouched while running tests.

- **Running tests**
  - Full test suite:
    ```bash
    php artisan test
    ```
  - Only student or attendance feature tests:
    ```bash
    php artisan test --filter=StudentTest
    php artisan test --filter=AttendanceTest
    ```

## Notes on search & indexing

- **Students search**
  - In production with MySQL, student name searches use a **FULLTEXT index** on the `name` column (plus a standard index on `student_id` for prefix searches).
  - In tests with SQLite (which does not support the same FULLTEXT syntax), the service automatically falls back to `LIKE`-based searching on both `name` and `student_id`.
- **Indexes**
  - `students`:
    - Unique index on `student_id`.
    - Index on `student_id` for lookups.
    - Composite index on `class`, `section`, `name` to optimize listing and ordering.
  - `attendances`:
    - Foreign-key indexes on `student_id` and `recorded_by`.
    - Unique index on (`student_id`, `date`) to prevent duplicates and speed up `updateOrCreate`.
    - Index on (`date`, `status`) for statistics and filtering.
