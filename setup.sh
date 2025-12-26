#!/bin/bash

# DJ Agency Platform Setup Script

echo "Setting up DJ Agency Platform..."

# Install PHP dependencies
echo "Installing PHP dependencies..."
composer install

# Install JavaScript dependencies
echo "Installing JavaScript dependencies..."
npm install

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
    
    # Generate application key
    echo "Generating application key..."
    php artisan key:generate
    
    # Prompt for database configuration
    echo "Please configure your database:"
    read -p "Database name (default: dj_agency): " DB_NAME
    DB_NAME=${DB_NAME:-dj_agency}
    
    read -p "Database username (default: root): " DB_USERNAME
    DB_USERNAME=${DB_USERNAME:-root}
    
    read -p "Database password: " DB_PASSWORD
    
    # Update .env file with database config
    sed -i "s/DB_DATABASE=laravel/DB_DATABASE=$DB_NAME/g" .env
    sed -i "s/DB_USERNAME=root/DB_USERNAME=$DB_USERNAME/g" .env
    sed -i "s/DB_PASSWORD=/DB_PASSWORD=$DB_PASSWORD/g" .env
fi

# Run migrations and seed database
echo "Running migrations and seeding database..."
php artisan migrate:fresh --seed

# Build frontend assets
echo "Building frontend assets..."
npm run dev

echo "Setup complete! Run 'php artisan serve' to start the development server."
echo ""
echo "Default users:"
echo "- Admin: admin@example.com / password"
echo "- DJ: dj@example.com / password"
echo "- Client: client@example.com / password"
echo "- Venue: venue@example.com / password"

echo ""
echo "IMPORTANT: Don't forget to manually add the admin middleware to app/Http/Kernel.php:"
echo "'can.admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,"
