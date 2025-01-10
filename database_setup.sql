-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS techgear_store;
USE techgear_store;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    category_id INT,
    stock INT DEFAULT 0,
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Create reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert sample users (password is in plaintext for vulnerability demonstration)
INSERT INTO users (username, password, email, full_name) VALUES
('admin', 'admin123', 'admin@techgear.com', 'Admin User'),
('john_doe', 'password123', 'john@example.com', 'John Doe'),
('jane_smith', 'password456', 'jane@example.com', 'Jane Smith');

-- Insert categories
INSERT INTO categories (name, description) VALUES
('Laptops', 'High-performance gaming and professional laptops'),
('Peripherals', 'Gaming mice, keyboards, and headsets'),
('Components', 'PC parts and hardware upgrades'),
('Monitors', 'Gaming and professional displays'),
('Accessories', 'Cables, adapters, and other accessories');

-- Insert products
INSERT INTO products (name, description, price, image_url, category_id, stock, featured) VALUES
('ASUS ROG Gaming Laptop', 'ASUS ROG Gaming Laptop with NVIDIA RTX 4060, Intel i9, 16GB RAM, 1TB NVMe SSD', 
 1499.99, 'https://m.media-amazon.com/images/I/71nz3cIcFOL._AC_SL1500_.jpg', 1, 15, 1),

('Logitech G502 Mouse', 'Logitech G502 HERO High Performance Gaming Mouse with 25K DPI Sensor', 
 79.99, 'https://m.media-amazon.com/images/I/61mpMH5TzkL._AC_SL1500_.jpg', 2, 50, 1),

('32" Gaming Monitor', 'LG 32" UltraGear QHD Gaming Monitor 165Hz 1ms with HDR 10', 
 499.99, 'https://m.media-amazon.com/images/I/81Vd+MkiHiL._AC_SL1500_.jpg', 4, 20, 1),

('Mechanical Keyboard', 'Razer BlackWidow V3 Pro Mechanical Wireless Gaming Keyboard', 
 179.99, 'https://m.media-amazon.com/images/I/71cngLX2xuL._AC_SL1500_.jpg', 2, 30, 1),

('RTX 4080 Graphics Card', 'NVIDIA GeForce RTX 4080 16GB GDDR6X Graphics Card', 
 1199.99, 'https://m.media-amazon.com/images/I/81oKhu2bGxL._AC_SL1500_.jpg', 3, 10, 1),

('Gaming Headset', 'HyperX Cloud II Wireless Gaming Headset with 7.1 Surround Sound', 
 149.99, 'https://m.media-amazon.com/images/I/71g3PB2uuxL._AC_SL1500_.jpg', 2, 40, 1);

-- Insert sample reviews
INSERT INTO reviews (product_id, user_id, rating, comment) VALUES
(1, 1, 5, 'Amazing gaming laptop! The RTX 4060 handles all my games perfectly.'),
(1, 2, 4, 'Great performance but runs a bit hot under heavy load.'),
(2, 1, 5, 'Best mouse I''ve ever used. Perfect for FPS games!'),
(2, 3, 4, 'Good build quality and comfortable grip.'); 