CREATE DATABASE IF NOT EXISTS community_skill_exchange;
USE community_skill_exchange;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS exchange_requests;
DROP TABLE IF EXISTS wanted_skills;
DROP TABLE IF EXISTS user_skills;
DROP TABLE IF EXISTS skills;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    location VARCHAR(150) DEFAULT NULL,
    profile_image VARCHAR(255) DEFAULT 'default-user.png',
    role ENUM('member', 'admin') NOT NULL DEFAULT 'member',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_users_email (email),
    INDEX idx_users_role (role)
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    UNIQUE KEY unique_category_name (category_name)
);

CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    skill_name VARCHAR(150) NOT NULL,
    category_id INT NOT NULL,
    description TEXT,
    UNIQUE KEY unique_skill_name (skill_name),
    CONSTRAINT fk_skills_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_skills_category (category_id)
);

CREATE TABLE user_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skill_id INT NOT NULL,
    experience_level VARCHAR(50) NOT NULL,
    description TEXT,
    CONSTRAINT fk_user_skills_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_skills_skill FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE,
    INDEX idx_user_skills_user (user_id),
    INDEX idx_user_skills_skill (skill_id)
);

CREATE TABLE wanted_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skill_id INT NOT NULL,
    description TEXT,
    CONSTRAINT fk_wanted_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wanted_skill FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE,
    INDEX idx_wanted_user (user_id),
    INDEX idx_wanted_skill (skill_id)
);

CREATE TABLE exchange_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    offered_skill_id INT NOT NULL,
    requested_skill_id INT NOT NULL,
    message TEXT,
    status ENUM('Pending', 'Accepted', 'Rejected', 'In Progress', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_exchange_sender FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_exchange_receiver FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_exchange_offered FOREIGN KEY (offered_skill_id) REFERENCES skills(id) ON DELETE CASCADE,
    CONSTRAINT fk_exchange_requested FOREIGN KEY (requested_skill_id) REFERENCES skills(id) ON DELETE CASCADE,
    INDEX idx_exchange_sender (sender_id),
    INDEX idx_exchange_receiver (receiver_id),
    INDEX idx_exchange_status (status)
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notification_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notifications_user (user_id),
    INDEX idx_notifications_read (is_read)
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exchange_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    reviewed_user_id INT NOT NULL,
    rating INT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_review_exchange FOREIGN KEY (exchange_id) REFERENCES exchange_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_review_reviewer FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_review_reviewed FOREIGN KEY (reviewed_user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_reviews_exchange (exchange_id),
    INDEX idx_reviews_rating (rating)
);

INSERT INTO categories (category_name, description) VALUES
('Web & Software', 'Development, design, and software support skills.'),
('Creative & Design', 'Visual, branding, and creative services.'),
('Business & Support', 'Administration, communication, and business help.'),
('Home & Lifestyle', 'Practical everyday support and community services.');

INSERT INTO skills (skill_name, category_id, description) VALUES
('Web Development', 1, 'Build websites and web applications.'),
('Graphic Design', 2, 'Create posters, logos, and digital visuals.'),
('Content Writing', 3, 'Create blog posts, website text, and branded content.'),
('Photography', 2, 'Capture events, portraits, and marketing visuals.'),
('Home Repair', 4, 'Fix common household issues and maintenance tasks.'),
('Tutoring', 3, 'Teach academic or practical subjects.'),
('Social Media Management', 3, 'Plan and manage online content and engagement.'),
('Data Entry', 3, 'Organize, input, and maintain digital records.');

INSERT INTO users (full_name, email, password, phone, location, profile_image, role) VALUES
('Alice Johnson', 'alice@example.com', '$2y$10$4o4sRMD9vGZXqEW6hgKwiORnj90JvnIh/jkP8DwgBV5YAgpKfo9x.', '0712345678', 'Lagos', 'default-user.png', 'member'),
('David Smith', 'david@example.com', '$2y$10$4o4sRMD9vGZXqEW6hgKwiORnj90JvnIh/jkP8DwgBV5YAgpKfo9x.', '0712345679', 'Accra', 'default-user.png', 'member'),
('Mary Green', 'mary@example.com', '$2y$10$4o4sRMD9vGZXqEW6hgKwiORnj90JvnIh/jkP8DwgBV5YAgpKfo9x.', '0712345680', 'Kumasi', 'default-user.png', 'member'),
('Admin User', 'admin@community.test', '$2y$10$jO.IAQqE1qtQVxiTSxmJle.w677p5cje2nXJ40BUhjFX7DsSMAzMC', '0700000001', 'Head Office', 'default-user.png', 'admin');

INSERT INTO user_skills (user_id, skill_id, experience_level, description) VALUES
(1, 1, 'Intermediate', 'Build responsive pages and landing sites.'),
(1, 6, 'Beginner', 'Support students with math and reading.'),
(2, 1, 'Advanced', 'Create modern dashboards and business websites.'),
(2, 3, 'Intermediate', 'Write landing page content and blog posts.'),
(2, 7, 'Intermediate', 'Plan social marketing campaigns.'),
(3, 2, 'Advanced', 'Create logos and social designs.'),
(3, 5, 'Experienced', 'Handle home repair requests.'),
(3, 1, 'Intermediate', 'Support front-end and web page design.'),
(4, 8, 'Advanced', 'Manage spreadsheets and digital records.');

INSERT INTO wanted_skills (user_id, skill_id, description) VALUES
(1, 2, 'I need help with flyers and branding for a community event.'),
(2, 2, 'Looking for a graphic designer for a small campaign.'),
(3, 1, 'Need help building a portfolio website.'),
(3, 7, 'Looking for support with social media marketing.'),
(4, 1, 'Admin needs a website audit and maintenance support.');

INSERT INTO exchange_requests (sender_id, receiver_id, offered_skill_id, requested_skill_id, message, status) VALUES
(1, 3, 1, 2, 'I can build a basic site and would love some design support.', 'Pending'),
(2, 1, 3, 2, 'I can write content and would like design help for my project.', 'Accepted'),
(3, 2, 2, 1, 'I can design your landing page if you help with web development.', 'Completed');

INSERT INTO notifications (user_id, message, is_read) VALUES
(1, 'You received a new exchange request.', 0),
(2, 'Your exchange request was accepted.', 0),
(3, 'A completed exchange was recorded.', 1);

INSERT INTO reviews (exchange_id, reviewer_id, reviewed_user_id, rating, comment) VALUES
(3, 3, 2, 5, 'David was responsive and the project was delivered professionally.');

SELECT 'Database initialized successfully.' AS status;
