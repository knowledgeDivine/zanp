-- ZANP database. Paste into phpMyAdmin > SQL tab and click Go.
CREATE DATABASE IF NOT EXISTS zanp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE zanp_db;

DROP TABLE IF EXISTS order_items, orders, products, categories, messages, subscribers, settings, users;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL UNIQUE,
  description TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  variant_index TINYINT UNSIGNED NOT NULL DEFAULT 0,
  tint VARCHAR(7) NOT NULL DEFAULT '#ffffff',
  image VARCHAR(255) NULL,
  model VARCHAR(255) NULL,
  stock INT NOT NULL DEFAULT 20,
  featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_cat (category_id),
  INDEX idx_featured (featured),
  FULLTEXT KEY ft_search (name, description),
  CONSTRAINT fk_prod_cat FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  customer_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  address VARCHAR(255) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  status ENUM('pending','paid','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user (user_id),
  CONSTRAINT fk_order_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  size TINYINT UNSIGNED NOT NULL,
  qty INT UNSIGNED NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_oi_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_oi_prod FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE subscribers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL UNIQUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE settings (
  k VARCHAR(50) PRIMARY KEY,
  v VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

INSERT INTO settings (k, v) VALUES
 ('site_name','ZANP'),
 ('tagline','Shoes built to be turned over in your hands.'),
 ('accent','#ff4d12');

-- Admin login: admin@zanp.com / Zanp@2026  (change it after your first login)
INSERT INTO users (name, email, password_hash, role) VALUES
 ('ZANP Admin','admin@zanp.com','$2y$10$P3WDpZM4X7P0yg/3NT0fX.MKax2KCBGb8OwVXD8vFLX0PMmtzcBVq','admin');

INSERT INTO categories (name, slug) VALUES
 ('Runners','runners'), ('Everyday','everyday'), ('Limited','limited');

INSERT INTO products (category_id, name, slug, description, price, variant_index, tint, stock, featured) VALUES
 (1,'Runner 01 Midnight','runner-01-midnight','A light knit runner on a soft foam sole. The darkest colourway, easy to wear with anything.',89000,0,'#ffffff',30,1),
 (1,'Runner 01 Beach','runner-01-beach','The same Runner 01 in a pale sand colourway that stays clean-looking on warm days.',89000,1,'#ffffff',25,1),
 (1,'Runner 01 Street','runner-01-street','A grey Runner 01 for wet pavements and long commutes.',89000,2,'#ffffff',25,1),
 (2,'Court Low Sand','court-low-sand','A low-profile everyday shoe with a warm sand tone and a flat grip sole.',74000,1,'#ffe6cf',18,1),
 (2,'Court Low Slate','court-low-slate','The Court Low in a cool slate tone for work days.',74000,2,'#d5e1ff',18,0),
 (2,'Court Low Ink','court-low-ink','A dark, simple Court Low that hides scuffs.',74000,0,'#e6e6ff',18,0),
 (3,'Drop 001 Rose','drop-001-rose','A small-batch colourway of the Runner with a soft rose tint. Limited run.',120000,1,'#ffc9d6',8,1),
 (3,'Drop 001 Mint','drop-001-mint','A small-batch colourway of the Runner with a pale mint tint. Limited run.',120000,2,'#c9ffe6',8,0);
