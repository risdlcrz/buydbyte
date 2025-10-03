-- SQL Updates for BuyDByte E-commerce System
-- Database: baitlog_luis

-- 1. Update users table role ENUM
ALTER TABLE users 
MODIFY role ENUM('admin','consumer','warehouse','finance') NOT NULL DEFAULT 'consumer';

-- 2. Create products table
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

-- 3. Add status column to users table if it doesn't exist
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS status ENUM('active','inactive','banned','pending_verification') DEFAULT 'pending_verification';

-- 4. Add phone_number column to users table if it doesn't exist
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS phone_number VARCHAR(20) UNIQUE;

-- 5. Add first_name and last_name columns if they don't exist
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS first_name VARCHAR(100) NOT NULL DEFAULT '';

ALTER TABLE users 
ADD COLUMN IF NOT EXISTS last_name VARCHAR(100);

-- 6. Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_users_status ON users(status);
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
