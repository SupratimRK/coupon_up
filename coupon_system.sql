CREATE DATABASE coupon_system;

USE coupon_system;

CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) UNIQUE,
    discount_amount INT,
    generation_date DATE,
    expiry_date DATE,
    is_used BOOLEAN DEFAULT 0
);
