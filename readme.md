## About this

This automation tool is designed to streamline the management of centers' reports.

## Installation

### Prerequisites
- PHP >= 7.2
- Composer
- MySQL or MariaDB
- Node.js and npm (for frontend assets)

### Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/parsasamandi/laravel-multi-organization-center.git
   cd laravel-multi-organization-center
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install
   ```

4. Create your environment configuration file:
   ```bash
   cp .env.example .env
   ```

5. Generate an application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database settings in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

7. Run database migrations:
   ```bash
   php artisan migrate
   ```

8. Build frontend assets:
   ```bash
   npm run dev
   ```
   Or for production:
   ```bash
   npm run production
   ```

9. Start the development server:
   ```bash
   php artisan serve
   ```

The application will be available at `http://localhost:8000`

## Copyright

The original theme and Admin panel are copyrighted by <a href="https://bootstrapmade.com/">BootstrapMade</a> and <a href="https://github.com/badranawad/adminlte-rtl">AdminLTE</a> respectively.
