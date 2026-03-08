-- database.sql

CREATE DATABASE IF NOT EXISTS catering_db;
USE catering_db;

CREATE TABLE IF NOT EXISTS menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category ENUM('Makanan', 'Minuman', 'Tambahan') NOT NULL DEFAULT 'Makanan',
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) UNIQUE NOT NULL, -- Merchant Ref ID / Tripay order ID
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    delivery_address TEXT NOT NULL,
    delivery_date DATE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    fee DECIMAL(10, 2) DEFAULT 0.00,
    payment_status VARCHAR(20) NOT NULL DEFAULT 'pending', -- pending, settlement, expire, cancel
    payment_method VARCHAR(50), -- Tripay Payment Method (e.g. BRIVA)
    payment_ref VARCHAR(255),   -- Tripay Core Reference
    payment_url VARCHAR(255),   -- Tripay Checkout URL
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL,
    menu_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL, -- Price at the time of order
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE
);

-- Insert sample menus
INSERT INTO menus (name, description, category, price, image) VALUES
('Paket Hemat A', 'Nasi Putih, Ayam Goreng, Sambal, Lalapan', 'Makanan', 25000, 'paket_a.jpg'),
('Paket Spesial B', 'Nasi Kuning, Ayam Bakar, Perkedel, Sambal Goreng Ati, Kerupuk', 'Makanan', 35000, 'paket_b.jpg'),
('Paket Premium C', 'Nasi Liwet, Rendang Daging, Telur Balado, Sayur Nangka, Kerupuk Udang', 'Makanan', 50000, 'paket_c.jpg'),
('Snack Box Manis', 'Kue Lumpur, Risoles, Lemper, Air Mineral', 'Makanan', 15000, 'snack_a.jpg'),
('Coffee Break Set', 'Kopi/Teh, 2 Macam Pastry', 'Makanan', 20000, 'coffee_break.jpg'),
('Es Teh Ceria', 'Es teh manis segar dengan lemon', 'Minuman', 5000, 'es_teh.jpg'),
('Kopi Savoria', 'Kopi susu gula aren signature kami', 'Minuman', 15000, 'kopi.jpg'),
('Puding Coklat', 'Puding coklat lembut dengan vla', 'Tambahan', 10000, 'puding.jpg'),
('Aneka Buah Potong', 'Semangka, Melon, Pepaya segar', 'Tambahan', 12000, 'buah.jpg');
