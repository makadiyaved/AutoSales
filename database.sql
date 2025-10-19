-- Create the database
CREATE DATABASE IF NOT EXISTS car_dealership;
USE car_dealership;

-- Create cars table
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    mileage INT NOT NULL,
    color VARCHAR(30) NOT NULL,
    transmission VARCHAR(20) NOT NULL,
    fuel_type VARCHAR(20) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    category VARCHAR(20) NOT NULL,
    status ENUM('available', 'sold', 'pending') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    remember_token VARCHAR(64) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create inquiries table
CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'contacted', 'closed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE SET NULL
);

-- Insert sample data for cars
INSERT INTO cars (make, model, year, price, mileage, color, transmission, fuel_type, description, image_url, category) VALUES
-- SUV Category
('Toyota', 'RAV4', 2023, 32000.00, 5000, 'Silver', 'Automatic', 'Gasoline', 'Spacious and reliable SUV with excellent fuel efficiency.', 'images/cars/toyota rav4.jpg', 'suv'),
('Honda', 'CR-V', 2023, 34000.00, 3000, 'Blue', 'Automatic', 'Gasoline', 'Comfortable and practical SUV with advanced safety features.', 'images/cars/honda cr-v.jpg', 'suv'),
('BMW', 'X5', 2023, 65000.00, 2000, 'Black', 'Automatic', 'Hybrid', 'Luxurious SUV with powerful performance and premium features.', 'images/cars/bmw x5.jpg', 'suv'),

-- Supercar Category
('Ferrari', 'F8 Tributo', 2023, 275000.00, 1000, 'Red', 'Automatic', 'Gasoline', 'Exhilarating performance and stunning design.', 'images/cars/ferrari-f8-tributo.jpg', 'supercar'),
('Lamborghini', 'Huracan', 2023, 245000.00, 800, 'Yellow', 'Automatic', 'Gasoline', 'Iconic supercar with breathtaking performance.', 'images/cars/lamborghini huracan.jpg', 'supercar'),
('McLaren', '720S', 2023, 295000.00, 500, 'Orange', 'Automatic', 'Gasoline', 'Cutting-edge technology meets extraordinary performance.', 'images/cars/mclaren.jpg', 'supercar'),

-- Luxury Category
('Mercedes-Benz', 'S-Class', 2023, 115000.00, 1500, 'Silver', 'Automatic', 'Hybrid', 'The pinnacle of luxury and comfort.', 'images/cars/mercedes-benz s-class.jpg', 'luxury'),
('Lexus', 'LS', 2023, 85000.00, 2000, 'White', 'Automatic', 'Hybrid', 'Japanese luxury with exceptional reliability.', 'images/cars/lexus ls.jpg', 'luxury'),
('Audi', 'A8', 2023, 95000.00, 1800, 'Black', 'Automatic', 'Gasoline', 'Sophisticated luxury with advanced technology.', 'images/cars/audi a8.jpg', 'luxury'),

-- Sports Car Category
('Porsche', '911', 2023, 125000.00, 1000, 'Black', 'Manual', 'Gasoline', 'Iconic sports car with perfect balance.', 'images/cars/porsche 911.jpg', 'sports'),
('Chevrolet', 'Corvette', 2023, 75000.00, 1500, 'Red', 'Automatic', 'Gasoline', 'American muscle with modern performance.', 'images/cars/chevrolet corvette.jpg', 'sports'),
('Nissan', 'GT-R', 2023, 115000.00, 1200, 'Grey', 'Automatic', 'Gasoline', 'Supercar performance at a sports car price.', 'images/cars/nissan gt-r.jpg', 'sports'),

-- Electric Category
('Tesla', 'Model S', 2023, 95000.00, 2000, 'White', 'Automatic', 'Electric', 'Luxury electric sedan with incredible range.', 'images/cars/tesla model s.jpg', 'electric'),
('Porsche', 'Taycan', 2023, 105000.00, 1500, 'Blue', 'Automatic', 'Electric', 'Electric performance with Porsche DNA.', 'images/cars/porsche taycan.jpg', 'electric'),
('Audi', 'e-tron GT', 2023, 102000.00, 1800, 'Silver', 'Automatic', 'Electric', 'Premium electric grand tourer.', 'images/cars/audi e-tron gt.jpg', 'electric'),

-- Classic Category
('Ford', 'Mustang', 1969, 85000.00, 45000, 'Red', 'Manual', 'Gasoline', 'Iconic muscle car in pristine condition.', 'images/cars/ford mustang.jpg', 'classic'),
('Chevrolet', 'Camaro', 1967, 75000.00, 38000, 'Blue', 'Manual', 'Gasoline', 'Classic American muscle car.', 'images/cars/chevrolet camaro.jpg', 'classic'),
('Porsche', '911', 1973, 120000.00, 42000, 'Yellow', 'Manual', 'Gasoline', 'Timeless classic in perfect condition.', 'images/cars/porsche 911.jpg', 'classic');

-- Insert sample admin user
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@autosales.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); 