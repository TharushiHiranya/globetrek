-- Test data for site initialization
-- Wipes old data and adds new data
-- Runs after db.sql

USE globetrek;

-- Clear old data (children first)
DELETE FROM queries;
DELETE FROM bookings;
DELETE FROM packages;
DELETE FROM users;

-- Reset ids
ALTER TABLE queries   AUTO_INCREMENT = 1;
ALTER TABLE bookings  AUTO_INCREMENT = 1;
ALTER TABLE packages  AUTO_INCREMENT = 1;
ALTER TABLE users     AUTO_INCREMENT = 1;

-- Add users
INSERT INTO users (name, email, password, role) VALUES
('Alice Admin',    'admin@globetrek.com',    'admin123',    'admin'),
('Sam Staff',      'staff@globetrek.com',    'staff123',    'staff'),
('Chris Customer', 'customer@globetrek.com', 'customer123', 'customer');

-- Add packages
INSERT INTO packages (title, destination, description, price, image_url, is_featured) VALUES
('Explore Machu Picchu',
 'Peru',
 'A 5-day adventure through the ancient Inca citadel of Machu Picchu, trekking breathtaking mountain trails with expert local guides.',
 1200.00,
 'images/destinations/machu.jpg', 1),

('Santorini Escape',
 'Greece',
 'Unwind in the iconic whitewashed villages of Santorini over 3 days of luxury sea-view stays and Mediterranean cuisine.',
 850.00,
 'images/destinations/santorini.jpg', 1),

('Costa Rica Wildlife',
 'Costa Rica',
 'A 7-day eco-adventure through lush rainforests, volcanic landscapes, and the richest biodiversity on the planet.',
 950.00,
 'images/destinations/costa_rica.jpg', 1),

('Tokyo City Explorer',
 'Japan',
 'Spend 6 days discovering neon-lit streets, ancient temples, world-class cuisine, and the timeless culture of Tokyo.',
 1500.00,
 'images/destinations/tokyo.jpg', 0),

('Safari in Serengeti',
 'Tanzania',
 'Witness the spectacular Great Migration on a 4-day safari across the vast Serengeti plains at golden hour.',
 2200.00,
 'images/destinations/serengeti.jpg', 0);

-- Add bookings
INSERT INTO bookings (user_id, package_id, status) VALUES
(3, 1, 'confirmed'),
(3, 3, 'pending');

-- Add contact messages
INSERT INTO queries (user_id, name, email, message) VALUES
(3,    'Chris Customer', 'customer@globetrek.com', 'Do you offer group discounts for the Machu Picchu trip?'),
(NULL, 'Jane Visitor',   'jane@example.com',       'I am interested in a custom honeymoon package. Please contact me.');
