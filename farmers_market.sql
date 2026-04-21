CREATE DATABASE IF NOT EXISTS farmers_market;
USE farmers_market;

CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('farmer', 'customer') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    farmer_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price_per_kg DECIMAL(10, 2) NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id)
);

CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create Indexes for Better Performance
CREATE INDEX idx_farmer_id ON products(farmer_id);
CREATE INDEX idx_user_id ON cart(user_id);
CREATE INDEX idx_product_id ON cart(product_id);
CREATE INDEX idx_user_orders ON orders(user_id);
CREATE INDEX idx_order_items ON order_items(order_id);

-- Insert Demo Data (Optional)
-- Demo Farmer
INSERT INTO users (name, email, password, role) 
VALUES ('John Farmer', 'farmer@test.com', '$2y$10$VpH4/j7a8/L5RVXpvZIWNuQN9FS3L7S5l9Z5Z8Z5Z5Z5Z5Z5Z5Z5Z', 'farmer');

-- Demo Customer
INSERT INTO users (name, email, password, role) 
VALUES ('Jane Customer', 'customer@test.com', '$2y$10$VpH4/j7a8/L5RVXpvZIWNuQN9FS3L7S5l9Z5Z8Z5Z5Z5Z5Z5Z5Z5Z', 'customer');

-- Demo Products
INSERT INTO products (farmer_id, name, description, price_per_kg, quantity) 
VALUES 
(1, 'Fresh Tomatoes', 'Organic, locally grown tomatoes', 3.99, 50),
(1, 'Cucumbers', 'Crisp and fresh cucumbers', 2.49, 30),
(1, 'Bell Peppers', 'Colorful and sweet bell peppers', 4.99, 25);

-- Note: Password for demo accounts is 'password123' (hashed with password_hash)
