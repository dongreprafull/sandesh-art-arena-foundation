-- Foundation portal tables (sandeshfoundation.artarenastudio.com)
-- Shares the same database as Personal + Marketplace (users, contact_enquiries already exist).

CREATE TABLE IF NOT EXISTS foundation_activities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category ENUM('activity','women_skill','student_community') NOT NULL DEFAULT 'activity',
  title VARCHAR(150) NOT NULL,
  description TEXT NULL,
  image VARCHAR(255) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS foundation_events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description TEXT NULL,
  event_date DATE NULL,
  location VARCHAR(150) NULL,
  image VARCHAR(255) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS foundation_gallery (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NULL,
  item_type ENUM('photo','video') NOT NULL DEFAULT 'photo',
  url VARCHAR(400) NOT NULL,
  album VARCHAR(100) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS volunteer_registrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  email VARCHAR(190) NULL,
  message TEXT NULL,
  status ENUM('new','contacted','confirmed') NOT NULL DEFAULT 'new',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS donations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  donor_name VARCHAR(120) NOT NULL,
  donor_email VARCHAR(190) NULL,
  donor_phone VARCHAR(20) NULL,
  amount DECIMAL(10,2) NOT NULL,
  campaign VARCHAR(150) NULL,
  payment_method ENUM('razorpay','upi') NOT NULL,
  payment_status ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending',
  razorpay_order_id VARCHAR(80) NULL,
  razorpay_payment_id VARCHAR(80) NULL,
  upi_proof_url VARCHAR(400) NULL,
  receipt_no VARCHAR(40) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Singleton settings row: About/Mission text + donation payment config.
CREATE TABLE IF NOT EXISTS foundation_settings (
  id INT PRIMARY KEY DEFAULT 1,
  about_text TEXT NULL,
  mission_text TEXT NULL,
  vision_text TEXT NULL,
  donation_policy_text TEXT NULL,
  upi_id VARCHAR(100) NULL,
  upi_qr_image VARCHAR(400) NULL,
  razorpay_key_id VARCHAR(100) NULL,
  razorpay_key_secret VARCHAR(100) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
