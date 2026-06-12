-- =============================================
-- BARBERBUS DATABASE SCHEMA
-- Run this SQL in phpMyAdmin or MySQL CLI
-- =============================================

CREATE DATABASE IF NOT EXISTS barberbus_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE barberbus_db;

-- ── USERS TABLE ──
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    phone       VARCHAR(20),
    password    VARCHAR(255) NOT NULL,
    role        ENUM('user','admin') DEFAULT 'user',
    avatar      VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ── BARBERS TABLE ──
CREATE TABLE IF NOT EXISTS barbers (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    specialty   VARCHAR(150),
    bio         TEXT,
    experience  INT DEFAULT 0,
    rating      DECIMAL(2,1) DEFAULT 5.0,
    photo       VARCHAR(255) DEFAULT NULL,
    is_active   TINYINT(1) DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── SERVICES TABLE ──
CREATE TABLE IF NOT EXISTS services (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT,
    price       DECIMAL(8,2) NOT NULL,
    duration    INT NOT NULL COMMENT 'Duration in minutes',
    category    VARCHAR(50) DEFAULT 'haircut',
    is_active   TINYINT(1) DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── BOOKINGS TABLE ──
CREATE TABLE IF NOT EXISTS bookings (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL,
    barber_id       INT,
    service_id      INT NOT NULL,
    booking_date    DATE NOT NULL,
    booking_time    TIME NOT NULL,
    status          ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',
    total_price     DECIMAL(8,2) NOT NULL,
    pay_method      VARCHAR(50) DEFAULT 'cash',
    notes           TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (barber_id)  REFERENCES barbers(id)  ON DELETE SET NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT
);

-- ── REVIEWS TABLE ──
CREATE TABLE IF NOT EXISTS reviews (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    booking_id  INT NOT NULL,
    barber_id   INT,
    rating      TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment     TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)   REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (barber_id) REFERENCES barbers(id)  ON DELETE SET NULL
);

-- ── SEED DEFAULT ADMIN ──
-- Password: admin123 (bcrypt hash)
INSERT IGNORE INTO users (name, email, phone, password, role) VALUES
('Admin BarberBus', 'admin@barberbus.com', '+60123456789',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ── SEED BARBERS ──
INSERT IGNORE INTO barbers (name, specialty, bio, experience, rating) VALUES
('Aariz Hakim',   'Fade & Taper Specialist', 'Master of precision fades with 8 years of experience. Trained in Kuala Lumpur and Singapore.', 8, 4.9),
('Danial Yusof',  'Classic Cuts & Beard', 'Specialises in classic barbering techniques and traditional straight-razor shaves.', 5, 4.8),
('Faiz Rahman',   'Modern Styles & Colour', 'Brings contemporary fashion cuts and hair colouring expertise to every appointment.', 4, 4.7),
('Hafizuddin',    'Curly & Textured Hair', 'Expert in managing and styling curly, wavy, and textured hair types.', 6, 4.8),
('Irfan Zaki',    'Skin Fade & Design', 'Creative barber specialising in skin fades and hair designs/patterns.', 3, 4.6);

-- ── SEED SERVICES ──
INSERT IGNORE INTO services (name, description, price, duration, category) VALUES
('Classic Haircut',     'Timeless cut with scissors and comb. Includes wash and style.', 25.00, 30, 'haircut'),
('Fade & Taper',        'Precision skin or mid-fade, tailored to your head shape.', 35.00, 45, 'haircut'),
('Beard Grooming',      'Shape, trim, and style your beard to perfection.', 20.00, 20, 'beard'),
('Hair & Beard Combo',  'Full haircut plus complete beard grooming service.', 50.00, 60, 'combo'),
('Kids Cut',            'Gentle and fun haircut for boys under 12 years old.', 18.00, 25, 'haircut'),
('Straight Razor Shave','Traditional hot-towel straight razor shave experience.', 30.00, 30, 'beard'),
('Hair Colour',         'Single colour treatment with premium dye products.', 80.00, 90, 'colour'),
('Highlights',          'Partial or full highlights for a natural sun-kissed look.', 120.00, 120, 'colour'),
('Scalp Treatment',     'Deep cleansing and conditioning scalp treatment.', 40.00, 40, 'treatment'),
('Premium Wash & Style','Luxury shampoo, conditioning treatment, and blowdry style.', 35.00, 35, 'treatment');

-- ── ADD OFFICER ROLE (run if already have the DB) ──
ALTER TABLE users MODIFY COLUMN role ENUM('user','admin','officer') DEFAULT 'user';

-- ── SEED SAMPLE OFFICER ACCOUNT ──
-- Password: officer123
INSERT IGNORE INTO users (name, email, phone, password, role) VALUES
('Aariz Hakim (Officer)', 'officer@barberbus.com', '+60198765432',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'officer');

-- ── STORE SETTINGS TABLE ──
CREATE TABLE IF NOT EXISTS store_settings (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(150) DEFAULT 'BarberBus',
    description     TEXT,
    is_open         TINYINT(1) DEFAULT 1,
    profile_image   VARCHAR(255),
    business_hours  VARCHAR(255),
    location        VARCHAR(255),
    phone           VARCHAR(20),
    email           VARCHAR(150),
    website         VARCHAR(255),
    updated_by      INT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ── PAGE CONTENT TABLE ──
CREATE TABLE IF NOT EXISTS page_content (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    page_name   VARCHAR(50) NOT NULL UNIQUE,
    title       VARCHAR(255),
    hero        TEXT,
    content     LONGTEXT,
    footer      VARCHAR(255),
    updated_by  INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ── PROMOTIONS TABLE ──
CREATE TABLE IF NOT EXISTS promotions (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200) NOT NULL,
    description TEXT,
    image       VARCHAR(255),
    link        VARCHAR(255),
    is_active   TINYINT(1) DEFAULT 1,
    start_date  DATE,
    end_date    DATE,
    created_by  INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ── INSERT DEFAULT STORE SETTINGS ──
INSERT IGNORE INTO store_settings (name, description, is_open, business_hours, location, phone) VALUES
('BarberBus', 'Premium barbershop experience in Kuala Lumpur', 1, 
 'Mon-Fri: 10:00 AM - 9:00 PM, Sat: 10:00 AM - 8:00 PM, Sun: 12:00 PM - 7:00 PM',
 '123 Jalan Merdeka, Kuala Lumpur', '+60123456789');

-- ── INSERT DEFAULT PAGE CONTENT ──
INSERT IGNORE INTO page_content (page_name, title, hero, content) VALUES
('dashboard', 'Dashboard', 'Welcome to BarberBus', 'Manage your barbershop and see real-time analytics.'),
('fashion', 'Fashion & Inspiration', 'Discover Your Style', 'Browse our collection of inspiring haircut styles.'),
('services', 'Our Services', 'Premium Barbering Services', 'We offer a wide range of professional barbering services.');
