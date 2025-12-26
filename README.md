# DJ Agency Platform

A Laravel and Vue SPA application for connecting DJs with clients and managing bookings, playlists, and venues.

## Features

- User management with multiple roles (Admin, DJ, Client, Venue)
- DJ profiles with genres, ratings, and booking information
- Booking system with status tracking and notifications
- Playlist creation and sharing
- Venue management and recurring booking options
- Advanced search and filtering
- Review and rating system
- Admin dashboard with analytics

## Requirements

- PHP 8.1+
- Composer
- Node.js & NPM
- MySQL

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/dj-agency.git
cd dj-agency
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Create a copy of the .env file:
```bash
cp .env.example .env
```

5. Generate an application key:
```bash
php artisan key:generate
```

6. Configure your database in the .env file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dj_agency
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Run the migrations and seed the database:
```bash
php artisan migrate --seed
```

8. Build frontend assets:
```bash
npm run dev
```

9. Start the development server:
```bash
php artisan serve
```

## Default Users

After seeding the database, you can log in with the following credentials:

- Admin: admin@example.com / password
- DJ: dj@example.com / password
- Client: client@example.com / password
- Venue: venue@example.com / password

## Development

- Run `npm run dev` for frontend development with hot reload
- Run `php artisan serve` for backend development

## Production

For production deployment:

1. Set the appropriate environment variables in .env
2. Build the frontend assets for production:
```bash
npm run build
```

3. Configure your web server (Nginx/Apache) to serve the application

## License

The application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
