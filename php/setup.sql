-- =============================================
-- BARBERBUS DATABASE SETUP
-- Run this in phpMyAdmin or MySQL CLI
-- =============================================

CREATE DATABASE IF NOT EXISTS barberbus_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE barberbus_db;

-- CLIENTS TABLE
CREATE TABLE IF NOT EXISTS clients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  phone VARCHAR(20) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- BARBERS TABLE
CREATE TABLE IF NOT EXISTS barbers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  specialty VARCHAR(255),
  rating DECIMAL(2,1) DEFAULT 5.0,
  status ENUM('active','off') DEFAULT 'active'
);

-- SERVICES TABLE
CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  price DECIMAL(8,2) NOT NULL,
  duration_min INT NOT NULL,
  category ENUM('cut','beard','treatment','package') DEFAULT 'cut'
);

-- BOOKINGS TABLE
CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ref_code VARCHAR(20) UNIQUE NOT NULL,
  client_id INT NOT NULL,
  barber_id INT,
  service_id INT NOT NULL,
  booking_date DATE NOT NULL,
  booking_time TIME NOT NULL,
  notes TEXT,
  pay_method ENUM('online_banking','ewallet','card','cash') DEFAULT 'cash',
  pay_status ENUM('paid','unpaid','refunded') DEFAULT 'unpaid',
  status ENUM('pending','confirmed','in_progress','done','cancelled') DEFAULT 'pending',
  amount DECIMAL(8,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (client_id) REFERENCES clients(id),
  FOREIGN KEY (barber_id) REFERENCES barbers(id),
  FOREIGN KEY (service_id) REFERENCES services(id)
);

-- ADMIN USERS TABLE
CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  name VARCHAR(100),
  role ENUM('admin','barber') DEFAULT 'barber',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── SEED DATA ──

INSERT INTO barbers (name, specialty, rating) VALUES
('Aariz Ahmad',  'Skin Fades, Classic Cuts, Beard Design', 5.0),
('Remy Syafiq',  'Fashion Cuts, Undercuts, Hair Colour',   4.9),
('Daniel Lim',   'Classic Cuts, Scalp Treatment, Hot Shave',4.8),
('Azri Kamal',   'Kids Cuts, Taper Fades, Beard Trim',     4.9),
('Fariz Hakim',  'Fades, Design Cuts, Beard Shaping',      4.8);

INSERT INTO services (name, price, duration_min, category) VALUES
('Classic Haircut',  25, 30, 'cut'),
('Skin Fade',        35, 45, 'cut'),
('Taper Fade',       35, 40, 'cut'),
('Undercut',         40, 45, 'cut'),
('Beard Trim',       20, 20, 'beard'),
('Hot Shave',        30, 30, 'beard'),
('Beard Design',     25, 25, 'beard'),
('Scalp Treatment',  45, 30, 'treatment'),
('Hair Colour',      80, 60, 'treatment'),
('Full Package',     80, 90, 'package'),
('Cut + Beard Combo',50, 60, 'package');

-- Default admin (password: admin123)
INSERT INTO admin_users (username, password_hash, name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Aariz Ahmad', 'admin');