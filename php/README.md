# Database Setup Instructions

## Prerequisites
- XAMPP, WAMP, MAMP, or similar local server environment with PHP and MySQL
- MySQL or MariaDB database server

## Steps to Set Up the Database

1. **Start your local server environment** (Apache and MySQL services)

2. **Create the database and tables**
   - Option 1: Using phpMyAdmin
     - Open phpMyAdmin (usually at http://localhost/phpmyadmin)
     - Create a new database named 'mealplanner'
     - Import the `setup_database.sql` file

   - Option 2: Using MySQL command line
     ```
     mysql -u root -p < setup_database.sql
     ```

3. **Configure the database connection**
   - If needed, update the database credentials in `db_connect.php`:
     - host: Usually 'localhost'
     - username: Default is 'root'
     - password: Default is empty ''
     - database: 'mealplanner'

## Testing the Connection
To test if your database connection is working:
1. Create a test.php file in the php directory
2. Add this code:
   ```php
   <?php
   require_once 'db_connect.php';
   echo "Database connection successful!";
   ?>
   ```
3. Access this file in your browser (e.g., http://localhost/meal2/php/test.php)

## Troubleshooting
- If you get a connection error, verify your MySQL service is running
- Check that the username and password in db_connect.php match your MySQL credentials
- Make sure the mealplanner database exists 