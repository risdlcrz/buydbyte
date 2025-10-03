# BuyDByte E-commerce System - Module 2

## CRUD For Accounts and Materials

This module provides complete CRUD (Create, Read, Update, Delete) functionality for managing users and products in the BuyDByte e-commerce system.

## Features

### User Management (Admin Only)
- **List Users**: View all users with search functionality and pagination
- **Add Users**: Create new user accounts with role-based permissions
- **Edit Users**: Update user information, roles, and status
- **Delete Users**: Remove users from the system (with safety checks)

### Product Management (Admin Only)
- **List Products**: View all products with search and category filtering
- **Add Products**: Create new products with image upload
- **Edit Products**: Update product details, pricing, and inventory
- **Delete Products**: Remove products from the system

## Database Setup

### 1. Update Users Table
```sql
ALTER TABLE users 
MODIFY role ENUM('admin','consumer','warehouse','finance') NOT NULL DEFAULT 'consumer';
```

### 2. Create Products Table
```sql
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_category (category),
    INDEX idx_price (price),
    INDEX idx_created_at (created_at)
);
```

### 3. Additional User Table Updates
```sql
-- Add status column if it doesn't exist
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS status ENUM('active','inactive','banned','pending_verification') DEFAULT 'pending_verification';

-- Add phone_number column if it doesn't exist
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS phone_number VARCHAR(20) UNIQUE;

-- Add first_name and last_name columns if they don't exist
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS first_name VARCHAR(100) NOT NULL DEFAULT '';
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS last_name VARCHAR(100);
```

## File Structure

```
buydbyte/
├── config/
│   └── database_connection.php    # Database connection and session management
├── uploads/
│   └── products/                  # Product image uploads directory
├── users.php                     # Users listing page
├── add_user.php                  # Add new user form
├── edit_user.php                 # Edit user form
├── delete_user.php              # Delete user confirmation
├── products.php                 # Products listing page
├── add_product.php              # Add new product form
├── edit_product.php             # Edit product form
├── delete_product.php           # Delete product confirmation
├── login.php                    # Admin login page
├── logout.php                   # Logout handler
└── database/
    └── sql_updates.sql          # SQL statements for database updates
```

## Installation & Setup

### 1. Database Configuration
Update the database connection settings in `config/database_connection.php`:
```php
private $host = 'localhost';
private $database = 'baitlog_luis';
private $username = 'root';
private $password = '';
```

### 2. Run SQL Updates
Execute the SQL statements in `database/sql_updates.sql` to update your database schema.

### 3. Create Upload Directory
Ensure the `uploads/products/` directory exists and has write permissions:
```bash
mkdir -p uploads/products
chmod 755 uploads/products
```

### 4. Create Admin User
You'll need at least one admin user to access the system. Insert an admin user directly into the database:
```sql
INSERT INTO users (first_name, last_name, email, password, role, status) 
VALUES ('Admin', 'User', 'admin@buydbyte.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');
-- Password: password
```

## Usage

### 1. Access the System
- Navigate to `login.php` in your browser
- Login with admin credentials
- You'll be redirected to the users management page

### 2. User Management
- **View Users**: Browse all users with search and pagination
- **Add User**: Click "Add New User" to create new accounts
- **Edit User**: Click the edit icon next to any user
- **Delete User**: Click the delete icon (with confirmation)

### 3. Product Management
- **View Products**: Browse all products with search and category filtering
- **Add Product**: Click "Add New Product" to create new inventory
- **Edit Product**: Click the edit icon next to any product
- **Delete Product**: Click the delete icon (with confirmation)

## Security Features

### Admin-Only Access
- All CRUD pages require admin authentication
- Non-admin users are redirected to login
- Session-based authentication with secure logout

### Data Validation
- Server-side validation for all forms
- Password hashing using PHP's `password_hash()`
- File upload validation for product images
- SQL injection prevention with prepared statements

### Safety Checks
- Users cannot delete their own accounts
- Confirmation dialogs for destructive actions
- Image file cleanup on product deletion
- Comprehensive error handling

## User Roles

- **Admin**: Full system access, can manage all users and products
- **Consumer**: Regular customer with shopping privileges
- **Warehouse**: Inventory management and order fulfillment
- **Finance**: Financial reports and payment processing

## Technical Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- PDO extension enabled
- File upload permissions
- Web server (Apache/Nginx)

## Features Included

✅ Complete CRUD operations for users and products  
✅ Admin-only access control  
✅ Search and pagination  
✅ Image upload for products  
✅ Password hashing  
✅ Session management  
✅ Responsive Bootstrap UI  
✅ Form validation  
✅ Error handling  
✅ Success/error messaging  
✅ Database connection reuse  

## Next Steps

This module provides the foundation for the e-commerce system. Future modules can build upon this to add:
- Customer shopping interface
- Order management
- Payment processing
- Inventory tracking
- Reporting and analytics
