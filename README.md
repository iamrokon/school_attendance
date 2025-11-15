# School Attendance System

A comprehensive Mini School Attendance System built with Laravel 10 (Backend) and Vue.js 3 (Frontend) with AI-assisted development workflow documentation.

## Features

### Backend (Laravel 10)
- ✅ Student Management (CRUD operations)
- ✅ Attendance Module with bulk recording
- ✅ Monthly attendance reports with eager loading
- ✅ Service Layer architecture
- ✅ Custom Artisan command for report generation
- ✅ Events/Listeners for attendance notifications
- ✅ Redis caching for attendance statistics
- ✅ Laravel Sanctum authentication
- ✅ RESTful API with Resource classes
- ✅ Request validation
- ✅ Unit tests

### Frontend (Vue.js 3)
- ✅ Student List Page with search/filter and pagination
- ✅ Attendance Recording Interface with bulk actions
- ✅ Real-time attendance percentage calculation
- ✅ Dashboard with today's attendance summary
- ✅ Monthly attendance chart using Chart.js
- ✅ Composition API
- ✅ Vue Router for navigation

## Prerequisites

- PHP >= 8.1
- Composer
- Node.js >= 16
- MySQL/PostgreSQL
- Redis (optional, for caching)

## Installation

### Backend Setup

1. Navigate to the backend directory:
```bash
cd backend
```

2. Install dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your `.env` file with database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_attendance
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Note: This project includes `predis/predis` in `backend/composer.json`. If you prefer Predis instead of the PHP `redis` extension, set `REDIS_CLIENT=predis` in your `.env` (shown above). If you haven't installed Predis manually, run `composer require predis/predis` in the `backend` directory.

6. Run migrations:
```bash
php artisan migrate
```

7. (Optional) Seed database with sample data:
```bash
php artisan db:seed
```

8. Start the development server:
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

### Frontend Setup

1. Navigate to the frontend directory:
```bash
cd frontend
```

2. Install dependencies:
```bash
npm install
```

3. Start the development server:
```bash
npm run dev
```

The frontend will be available at `http://localhost:3000`

## API Endpoints

### Authentication
- `POST /api/register` - Register a new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user (requires auth)

### Students
- `GET /api/students` - List all students (with filters: class, section, search)
- `POST /api/students` - Create a new student
- `GET /api/students/{id}` - Get a specific student
- `PUT /api/students/{id}` - Update a student
- `DELETE /api/students/{id}` - Delete a student

### Attendance
- `GET /api/attendances` - List attendance records (with filters: date, student_id, status)
- `POST /api/attendances` - Record bulk attendance
- `GET /api/attendances/{id}` - Get a specific attendance record
- `GET /api/attendances/reports/monthly?month=2024-01&class=10` - Get monthly report
- `GET /api/attendances/statistics/today` - Get today's statistics

## Artisan Commands

### Generate Attendance Report
```bash
php artisan attendance:generate-report {month} {class?}
```

Example:
```bash
php artisan attendance:generate-report 2024-01
php artisan attendance:generate-report 2024-01 10
```

## Testing

Run the test suite:
```bash
cd backend
php artisan test
```

## Database Setup

The system uses the following main tables:
- `students` - Student information
- `attendances` - Attendance records
- `users` - System users (teachers/admins)
- `personal_access_tokens` - Sanctum tokens

## Project Structure

```
school_attendance/
├── backend/
│   ├── app/
│   │   ├── Console/Commands/        # Artisan commands
│   │   ├── Events/                  # Events
│   │   ├── Http/
│   │   │   ├── Controllers/Api/     # API Controllers
│   │   │   ├── Requests/            # Form Requests
│   │   │   └── Resources/           # API Resources
│   │   ├── Listeners/               # Event Listeners
│   │   ├── Models/                  # Eloquent Models
│   │   └── Services/                # Service Layer
│   ├── database/
│   │   ├── factories/               # Model Factories
│   │   └── migrations/              # Database Migrations
│   └── tests/                       # Tests
├── frontend/
│   ├── src/
│   │   ├── views/                   # Vue Components
│   │   ├── services/                # API Service
│   │   └── App.vue                  # Root Component
│   └── package.json
└── README.md
```

## Technologies Used

### Backend
- Laravel 10
- Laravel Sanctum
- Redis (for caching)
- MySQL/PostgreSQL

### Frontend
- Vue.js 3 (Composition API)
- Vue Router
- Axios
- Chart.js
- Vite

## License

This project is open-sourced software licensed under the MIT license.

## Author

Built with AI assistance (Claude/Cursor) - See AI_WORKFLOW.md for details.

