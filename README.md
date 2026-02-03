Library Management System
A PHP-based web application for managing library book collections with an admin panel interface. The system provides comprehensive book inventory management, user registration, and secure admin-only access control.
________________________________________
Project Overview
This Library Management System is built using PHP (Backend), MySQL(Database), and the Twig template engine. It allows administrators to manage book inventory through features like add(Create),  editing(Update), searching, and deleting(Delete) books, along with user account management capabilities.
Technology Stack:
•	PHP 7.4+ with PDO
•	MySQL/MariaDB
•	Twig Template Engine (v3.23)
•	HTML5, CSS3, JavaScript
•	Font Awesome Icons
System Requirements:
•	Web Server: Apache (XAMPP/LAMP)
•	PHP 7.4 or higher
•	MySQL 5.7+ or MariaDB 10.3+
•	Composer
•	Modern web browser
________________________________________
Setup Instructions
Step 1: Server Installation
For XAMPP (Windows/Mac/Linux):
1.	Download and install XAMPP from https://www.apachefriends.org/
2.	Start Apache and MySQL services from the XAMPP Control Panel
3.	Extract project files to C:\xampp\htdocs\LibraryManagementSystem (Windows) or /opt/lampp/htdocs/LibraryManagementSystem (Linux/Mac)
For LAMP (Linux):
sudo apt update
sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql php-mbstring
sudo mv LibraryManagementSystem /var/www/html/
sudo chown -R www-data:www-data /var/www/html/LibraryManagementSystem
Step 2: Database Configuration
1.	Access phpMyAdmin at http://localhost/phpmyadmin
2.	Create new database: library_db with collation utf8_general_ci
3.	Execute the following SQL to create required tables:
-- Users table
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Books table
CREATE TABLE `books` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(100) NOT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `published_year` VARCHAR(4) DEFAULT NULL,
  `isbn` VARCHAR(13) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_title` (`title`),
  INDEX `idx_author` (`author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
Step 3: Insert Default Admin Account
Important: Admin credentials must be manually inserted into the database. Execute this SQL command:
INSERT INTO `users` (`name`, `email`, `password`, `role`) 
VALUES (
  'Administrator', 
  'Admin@123gmail.com', 
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'admin'
);
Default Admin Credentials:
•	Email: Admin@123gmail.com
•	Password: Admin@!123
Step 4: Install Dependencies
Navigate to project directory and install Composer dependencies:
cd /path/to/LibraryManagementSystem
composer install
If Composer is not installed, download from https://getcomposer.org/
Step 5: Configure Database Connection
Verify the database settings in config/db.php:
<?php
$host = "localhost";
$dbname = "library_db";
$user = "root";
$pass = "";  // Empty for XAMPP default
Step 6: Access Application
Open browser and navigate to: http://localhost/LibraryManagementSystem/public/login.php
________________________________________
Login/Signup Logic
Admin Access
Only administrators can access the system's management panel. The admin account must be created manually in the database.
Default Admin Credentials:
•	Email: Admin@123gmail.com
•	Password: Admin@!123
Critical Notes:
•	No admin signup form exists in the application
•	Admin credentials must be directly inserted into the database using the SQL command provided above
•	The login system checks both credentials AND the role = 'admin' field
•	Regular users attempting to log in will receive: "Access denied. Admin only."
User Registration
The signup page allows creation of regular user accounts with the following requirements:
Password Requirements:
•	Minimum 6 characters
•	At least one uppercase letter
•	At least one lowercase letter
•	At least one number
•	At least one special character (!@#$%^&*(),.?":{}|<>)
Important: All users created through signup have role = 'user' and cannot access the admin panel.
________________________________________
UI Notes
Current Functionality Status
✅ Fully Functional (Admin Panel Only):
•	Admin authentication and login system
•	Dashboard with statistics
•	Book management (Add/Edit/Delete)
•	Book search functionality
•	User account viewing and deletion
⚠️ Dummy/Placeholder Features:
•	Category Section: While categories can be selected when adding books and are stored in the database, there is no comprehensive category management system. The category feature is essentially a placeholder for future development.
❌ Not Implemented:
•	User Panel: Regular users cannot log in to view books or access any user-facing features. The system currently has no front-end interface for registered users. Users can only sign up and have their accounts managed by administrators.
________________________________________
Features Implemented
Authentication & Security
•	Secure admin-only login system
•	Password hashing using PHP's password_hash() and password_verify()
•	Session management with session_regenerate_id() for security
•	CSRF token protection on all forms
•	Role-based access control (admin verification)
•	SQL injection protection via PDO prepared statements
•	XSS protection through htmlspecialchars() output escaping
Book Management
•	Add Books: Comprehensive form with validation for title, author, category, publication date, and ISBN
•	Edit Books: Modify existing book information with pre-filled forms
•	Delete Books: Remove books with JavaScript confirmation dialogs
•	Search Books: Filter by title (partial match), category (exact), and year
•	Duplicate Prevention: System checks if book with same title and author already exists
•	Recent Books Display: Dashboard shows the 7 most recently added books
Validation Rules
Book Title:
•	2-100 characters required
•	Allows alphanumeric characters and common punctuation (.,'-!?:#)
Author Name:
•	2-50 characters required
•	Must start with a letter
•	Allows letters, spaces, periods, apostrophes, and hyphens
ISBN:
•	Must be exactly 10 or 13 digits
•	Numeric characters only
Publication Year:
•	Date format required (YYYY-MM-DD)
•	Cannot be in the future
•	Must be after year 1500
User Management
•	User Registration: Public signup form with comprehensive validation
•	View Users: Admin can see all registered users with name, email, and role
•	Delete Users: Admin can remove user accounts (admin accounts are protected from deletion)
•	Role Display: Visual badges showing user roles
•	Account Statistics: Dashboard displays total registered users
Dashboard Statistics
The admin dashboard provides quick overview with:
•	Total books count
•	Total registered users (excluding admins)
•	Total categories (placeholder count)
•	Recent additions to library (last 7 books)
User Interface Elements
•	Clean, modern design with consistent styling
•	Font Awesome icons throughout the interface
•	Color-coded alert messages (success in green, errors in red)
•	Responsive tables for data display
•	Confirmation dialogs for destructive actions
•	Empty state messages when no data is available
•	Form validation feedback with error highlighting
________________________________________
Database Structure
users Table
Stores user account information including authentication credentials and role assignment.
Column	Type	Description
id	INT(11) AUTO_INCREMENT	Unique user identifier
name	VARCHAR(100)	User's full name
email	VARCHAR(100) UNIQUE	Login email address
password	VARCHAR(255)	Hashed password
role	ENUM('user', 'admin')	User access level (default: 'user')
created_at	TIMESTAMP	Account creation timestamp
Indexes: Primary key on id, index on email and role for query optimization.
books Table
Stores the library's book inventory with complete bibliographic information.
Column	Type	Description
id	INT(11) AUTO_INCREMENT	Unique book identifier
title	VARCHAR(255)	Book title
author	VARCHAR(100)	Author name
category	VARCHAR(100)	Book category (nullable)
published_year	VARCHAR(4)	Year of publication (nullable)
isbn	VARCHAR(13)	ISBN number (nullable)
created_at	TIMESTAMP	Record creation timestamp
updated_at	TIMESTAMP	Last modification timestamp
Indexes: Primary key on id, indexes on title and author for search performance.
Sample Data
Required Admin Account:
INSERT INTO users (name, email, password, role) VALUES
('Administrator', 'Admin@123gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
Optional Sample Books:
INSERT INTO books (title, author, category, published_year, isbn) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', 'Fiction', '1925', '9780743273565'),
('1984', 'George Orwell', 'Science Fiction', '1949', '9780451524935'),
('To Kill a Mockingbird', 'Harper Lee', 'Fiction', '1960', '9780061120084');
________________________________________
Known Issues & Limitations
Critical Issues
1.	Manual Admin Setup Required: No automated installation script exists. The admin account must be created manually in the database using the SQL command provided. The password must be pre-hashed before insertion.
2.	No Database Import File: Users must manually execute CREATE TABLE statements individually rather than importing a single .sql file for initial setup.
Functional Limitations
1.	Category System is Placeholder: Categories can be added to books and stored in the database, but there is no category management interface. The category count on the dashboard may not reflect actual distinct categories accurately.
2.	No User-Facing Interface: The system has no front-end for regular users. Users can register accounts but cannot log in to view or interact with the book catalog. This is admin-only software.
3.	No Password Recovery: Users who forget their password have no self-service reset option. Admin intervention is required to update passwords directly in the database.
4.	Limited Book Information: The system doesn't support:
o	Book cover images
o	Book descriptions or summaries
o	Multiple authors per book
o	Book availability status
o	Borrowing/checkout functionality
5.	Basic Search Only: Search functionality is limited to exact matches for category and year, with only partial matching available for title searches.
Security & Production Considerations
1.	Default Credentials: The provided default admin credentials must be changed immediately after first login for production use.
2.	Error Display: Database errors are displayed directly to users instead of being logged securely. In production, set display_errors = Off in php.ini.
3.	No Rate Limiting: The login form has no protection against brute force attacks. Consider implementing rate limiting or login attempt tracking.
4.	Session Management: No automatic session timeout is configured. Users remain logged in indefinitely until manual logout.
________________________________________
Troubleshooting
Database Connection Failed:
•	Verify MySQL service is running
•	Check credentials in config/db.php
•	Ensure database library_db exists
•	Confirm MySQL user has appropriate permissions
Cannot Access Admin Panel:
•	Verify admin user exists with role = 'admin'
•	Confirm email and password are correct
•	Check that PHP sessions are enabled
•	Clear browser cookies and cache
Composer Dependencies Error:
# Run in project directory
composer install
CSRF Token Invalid:
•	Clear browser cookies
•	Ensure session_start() is called before token generation
•	Check that sessions are working properly
________________________________________
Project Structure
LibraryManagementSystem/
├── assets/
│   ├── css/                # Stylesheets
│   ├── image/              # Static images
│   └── js/                 # Client-side scripts
├── config/
│   └── db.php              # Database connection
├── includes/
│   ├── admin_auth.php      # Authentication guard
│   ├── header.php          # Page header template
│   └── footer.php          # Page footer template
├── public/                 # Web-accessible files
│   ├── login.php           # Admin login
│   ├── signup.php          # User registration
│   ├── index.php           # Dashboard
│   ├── add.php             # Add book form
│   ├── edit.php            # Edit book form
│   ├── search.php          # Search interface
│   ├── users.php           # User management
│   ├── delete.php          # Delete book handler
│   └── delete_user.php     # Delete user handler
├── templates/              # Twig templates
├── vendor/                 # Composer dependencies
└── composer.json           # Dependency configuration
________________________________________
Security Best Practices
1.	Change Default Password: Immediately update the admin password after first login
2.	Set MySQL Password: Configure a strong password for the MySQL root user
3.	Production Environment: 
o	Disable error display: display_errors = Off
o	Enable error logging: log_errors = On
o	Implement HTTPS with SSL certificate
o	Set restrictive file permissions (644 for files, 755 for directories)
4.	Regular Backups: Schedule automated database backups
________________________________________

Contact Information
Student: [Furpa Lama]
Email: [NP03CS4A240224@heraldcollege.edu.np]
Student ID: NP03CS4A240224
Uni_ID: 2501398
Last Updated: February 2026


