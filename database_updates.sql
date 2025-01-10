-- Create reviews table if it doesn't exist
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

-- Add some sample reviews
INSERT INTO reviews (product_id, user_id, rating, comment) VALUES
(1, 1, 5, 'Amazing gaming laptop! The RTX 4060 handles all my games perfectly.'),
(1, 2, 4, 'Great performance but runs a bit hot under heavy load.'),
(2, 1, 5, 'Best mouse I''ve ever used. Perfect for FPS games!'),
(2, 3, 4, 'Good build quality and comfortable grip.');

-- Update categories and products
INSERT INTO categories (name, description) VALUES
('Laptops', 'High-performance gaming and professional laptops'),
('Peripherals', 'Gaming mice, keyboards, and headsets'),
('Components', 'PC parts and hardware upgrades'),
('Monitors', 'Gaming and professional displays'),
('Accessories', 'Cables, adapters, and other accessories');

-- Update existing products with better images and descriptions
UPDATE products SET 
    image_url = 'https://m.media-amazon.com/images/I/71nz3cIcFOL._AC_SL1500_.jpg',
    description = 'ASUS ROG Gaming Laptop with NVIDIA RTX 4060, Intel i9, 16GB RAM, 1TB NVMe SSD',
    category_id = 1,
    stock = 15,
    featured = 1
WHERE id = 1;

UPDATE products SET 
    image_url = 'https://m.media-amazon.com/images/I/61mpMH5TzkL._AC_SL1500_.jpg',
    description = 'Logitech G502 HERO High Performance Gaming Mouse with 25K DPI Sensor',
    category_id = 2,
    stock = 50,
    featured = 1
WHERE id = 2;

-- Add new products
INSERT INTO products (name, description, price, image_url, category_id, stock, featured) VALUES
('32" Gaming Monitor', 'LG 32" UltraGear QHD Gaming Monitor 165Hz 1ms with HDR 10', 
 499.99, 'https://m.media-amazon.com/images/I/81Vd+MkiHiL._AC_SL1500_.jpg', 4, 20, 1),

('Mechanical Keyboard', 'Razer BlackWidow V3 Pro Mechanical Wireless Gaming Keyboard', 
 179.99, 'https://m.media-amazon.com/images/I/71cngLX2xuL._AC_SL1500_.jpg', 2, 30, 1),

('RTX 4080 Graphics Card', 'NVIDIA GeForce RTX 4080 16GB GDDR6X Graphics Card', 
 1199.99, 'https://m.media-amazon.com/images/I/81oKhu2bGxL._AC_SL1500_.jpg', 3, 10, 1),

('Gaming Headset', 'HyperX Cloud II Wireless Gaming Headset with 7.1 Surround Sound', 
 149.99, 'https://m.media-amazon.com/images/I/71g3PB2uuxL._AC_SL1500_.jpg', 2, 40, 1);

-- Update existing products with new images
UPDATE products SET 
    image_url = 'https://m.media-amazon.com/images/I/71nz3cIcFOL._AC_SL1500_.jpg',
    description = 'ASUS ROG Strix G16 Gaming Laptop with NVIDIA RTX 4060, Intel i9-13980HX, 16GB DDR5, 1TB PCIe SSD'
WHERE name LIKE '%ASUS%';

-- Add more products with verified working images
INSERT INTO products (name, description, price, image_url, category_id, stock, featured) VALUES
('MSI Gaming Laptop', 'MSI Stealth 16 Studio Gaming Laptop: 16" QHD+, RTX 4070, Intel i9', 
 1899.99, 'https://m.media-amazon.com/images/I/71YWU4GO3-L._AC_SL1500_.jpg', 1, 10, 1),

('Razer Blade 15', 'Razer Blade 15 Advanced Gaming Laptop: 15.6" 4K OLED, RTX 3080 Ti', 
 2499.99, 'https://m.media-amazon.com/images/I/71wF7YDIQkL._AC_SL1500_.jpg', 1, 5, 1),

('Samsung Odyssey Monitor', 'Samsung 49" Odyssey G9 Gaming Monitor - Dual QHD, 240Hz', 
 1299.99, 'https://m.media-amazon.com/images/I/81v6oW04OvL._AC_SL1500_.jpg', 4, 8, 1),

('Corsair K100 RGB', 'Corsair K100 RGB Mechanical Gaming Keyboard - Cherry MX Speed', 
 229.99, 'https://m.media-amazon.com/images/I/71YoQH2qExL._AC_SL1500_.jpg', 2, 25, 1),

('Razer DeathAdder V3', 'Razer DeathAdder V3 Pro Wireless Gaming Mouse - 30K DPI', 
 149.99, 'https://m.media-amazon.com/images/I/61IYYoZ66VL._AC_SL1500_.jpg', 2, 30, 1),

('ASUS ROG STRIX 850W', 'ASUS ROG STRIX 850W Gold PSU - Fully Modular Power Supply', 
 169.99, 'https://m.media-amazon.com/images/I/81d6UHj6PoL._AC_SL1500_.jpg', 3, 15, 1),

('SteelSeries Arctis Pro', 'SteelSeries Arctis Pro Wireless Gaming Headset with Dual Battery', 
 329.99, 'https://m.media-amazon.com/images/I/81ptRfQn8fL._AC_SL1500_.jpg', 2, 20, 1),

('ASUS TUF Gaming Monitor', 'ASUS TUF 27" 2K HDR Gaming Monitor - 170Hz 1ms VRR', 
 399.99, 'https://m.media-amazon.com/images/I/81+MZNp93LS._AC_SL1500_.jpg', 4, 12, 1),

('Logitech G Pro X', 'Logitech G Pro X Superlight Wireless Gaming Mouse - 25K DPI', 
 159.99, 'https://m.media-amazon.com/images/I/61GTK3XJokL._AC_SL1500_.jpg', 2, 40, 1),

('RTX 4070 Ti', 'ASUS ROG STRIX GeForce RTX 4070 Ti OC Edition 12GB GDDR6X', 
 899.99, 'https://m.media-amazon.com/images/I/81oZx8ZR8fL._AC_SL1500_.jpg', 3, 7, 1);

-- Add some budget-friendly options
INSERT INTO products (name, description, price, image_url, category_id, stock, featured) VALUES
('Redragon K552', 'Redragon K552 Mechanical Gaming Keyboard RGB LED Rainbow', 
 34.99, 'https://m.media-amazon.com/images/I/71cngLX2xuL._AC_SL1500_.jpg', 2, 50, 0),

('Havit Keyboard Mouse Combo', 'Havit Mechanical Keyboard and Mouse Combo RGB Gaming Set', 
 39.99, 'https://m.media-amazon.com/images/I/71geyZqH5pL._AC_SL1500_.jpg', 2, 45, 0),

('ViewSonic Monitor', 'ViewSonic 24" 1080p 144Hz Gaming Monitor with FreeSync', 
 159.99, 'https://m.media-amazon.com/images/I/71rXSVqET9L._AC_SL1500_.jpg', 4, 25, 0); 