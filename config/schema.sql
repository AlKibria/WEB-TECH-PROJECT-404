

-- Step 1: Create tables in correct FK order

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('reader','author','editor','admin') DEFAULT 'reader',
    bio TEXT,
    profile_pic VARCHAR(255),
    social_links JSON,
    is_active TINYINT(1) DEFAULT 1,
    is_author_approved TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) UNIQUE NOT NULL,
    description TEXT,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    created_by INT,
    usage_count INT DEFAULT 0,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS series (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    cover_image_path VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author_id INT NOT NULL,
    editor_id INT,
    category_id INT,
    series_id INT,
    series_order INT DEFAULT 0,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(300) UNIQUE NOT NULL,
    body LONGTEXT NOT NULL,
    excerpt TEXT,
    featured_image_path VARCHAR(255),
    status ENUM('draft','submitted','revision_requested','approved','published','unpublished') DEFAULT 'draft',
    editor_feedback TEXT,
    scheduled_publish_at DATETIME,
    published_at DATETIME,
    view_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (editor_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (series_id) REFERENCES series(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS article_tags (
    article_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS article_revisions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    author_id INT NOT NULL,
    body_snapshot LONGTEXT NOT NULL,
    saved_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (article_id, user_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    user_id INT NOT NULL,
    body TEXT NOT NULL,
    parent_id INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comment_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment_id INT NOT NULL,
    reporter_id INT NOT NULL,
    reason TEXT,
    status ENUM('pending','resolved') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
    FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS follows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL,
    followed_author_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_follow (follower_id, followed_author_id),
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (followed_author_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reading_lists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    article_id INT NOT NULL,
    saved_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reading_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    article_id INT NOT NULL,
    read_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS editorial_calendar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    editor_id INT NOT NULL,
    scheduled_date DATETIME,
    note TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (editor_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS author_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    motivation TEXT NOT NULL,
    writing_sample TEXT NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    reviewed_by INT,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);



INSERT INTO users (id, name, username, email, password_hash, role, bio, social_links, is_active, is_author_approved) VALUES
(1, 'Admin User',    'admin',   'admin@example.com',   '$2y$10$3yJdesYYt421XnzeAbn3fegeGZtcho2U9UEIURyp7/HizEds7dwjq', 'admin',  'Platform administrator.', '{}', 1, 0),
(2, 'Alex Johnson',  'alexj',   'alex@example.com',    '$2y$10$3yJdesYYt421XnzeAbn3fegeGZtcho2U9UEIURyp7/HizEds7dwjq', 'author', 'Passionate writer covering tech, culture, and society.', '{"twitter":"@alexj","linkedin":"alexjohnson","github":"alexj"}', 1, 1),
(3, 'Reader One',    'reader1', 'reader1@example.com', '$2y$10$3yJdesYYt421XnzeAbn3fegeGZtcho2U9UEIURyp7/HizEds7dwjq', 'reader', NULL, '{}', 1, 0),
(4, 'Reader Two',    'reader2', 'reader2@example.com', '$2y$10$3yJdesYYt421XnzeAbn3fegeGZtcho2U9UEIURyp7/HizEds7dwjq', 'reader', NULL, '{}', 1, 0),
(5, 'Reader Three',  'reader3', 'reader3@example.com', '$2y$10$3yJdesYYt421XnzeAbn3fegeGZtcho2U9UEIURyp7/HizEds7dwjq', 'reader', NULL, '{}', 1, 0),
(6, 'Reader Four',   'reader4', 'reader4@example.com', '$2y$10$3yJdesYYt421XnzeAbn3fegeGZtcho2U9UEIURyp7/HizEds7dwjq', 'reader', NULL, '{}', 1, 0);

INSERT INTO categories (id, name, slug, description, created_by) VALUES
(1, 'Technology', 'technology', 'Tech news and tutorials', 1),
(2, 'Culture',    'culture',    'Arts, society and culture', 1),
(3, 'Science',    'science',    'Science discoveries', 1),
(4, 'Business',   'business',   'Business and entrepreneurship', 1);

INSERT INTO tags (id, name, slug, created_by, usage_count) VALUES
(1, 'PHP',         'php',         2, 3),
(2, 'JavaScript',  'javascript',  2, 5),
(3, 'Web Dev',     'web-dev',     2, 7),
(4, 'AI',          'ai',          2, 4),
(5, 'Open Source', 'open-source', 2, 2);

INSERT INTO series (id, author_id, title, description) VALUES
(1, 2, 'Modern Web Development', 'A complete guide to building modern web apps'),
(2, 2, 'AI and Society',         'Exploring how AI is changing our world');

INSERT INTO articles (id, author_id, category_id, series_id, series_order, title, slug, body, excerpt, status, view_count, published_at) VALUES
(1, 2, 1, 1, 1, 'Getting Started with PHP MVC',
 'getting-started-php-mvc-001',
 '<p>Model-View-Controller (MVC) is a software design pattern that separates application logic into three components: the <strong>Model</strong> manages data, the <strong>View</strong> handles the user interface, and the <strong>Controller</strong> processes user requests.</p><p>In PHP, implementing MVC from scratch gives you complete control over your application structure without the overhead of a full framework.</p>',
 'Learn how to implement MVC architecture in PHP from scratch.',
 'published', 245, NOW() - INTERVAL 10 DAY),

(2, 2, 1, 1, 2, 'Building RESTful APIs with PHP',
 'building-restful-apis-php-002',
 '<p>REST (Representational State Transfer) is an architectural style for building web services. In this tutorial we will build a complete RESTful API using plain PHP without any framework.</p><p>We will cover routing, request handling, JSON responses, and authentication using API tokens.</p>',
 'A step-by-step guide to building REST APIs with PHP and MySQL.',
 'published', 189, NOW() - INTERVAL 7 DAY),

(3, 2, 2, 2, 1, 'How AI is Reshaping Creative Industries',
 'ai-reshaping-creative-003',
 '<p>Artificial intelligence is no longer a futuristic concept — it is actively transforming how we create music, write stories, design graphics, and produce films. Tools like DALL-E, Midjourney, and ChatGPT have moved from research labs into mainstream creative workflows.</p>',
 'Exploring the deep impact of AI tools on artists, writers, and creators.',
 'submitted', 0, NULL),

(4, 2, 1, NULL, 0, 'Understanding JavaScript Promises',
 'javascript-promises-004',
 '<p>Promises are a fundamental part of modern JavaScript. They represent a value that may be available now, in the future, or never. Understanding how to work with Promises is essential for any modern JavaScript developer.</p>',
 'A deep dive into JavaScript Promises and async/await.',
 'draft', 0, NULL),

(5, 2, 3, NULL, 0, 'The Future of Quantum Computing',
 'quantum-computing-005',
 '<p>Quantum computing harnesses quantum mechanical phenomena such as superposition and entanglement to process information in fundamentally different ways than classical computers.</p>',
 'What quantum computing means for the next decade of technology.',
 'revision_requested', 0, NULL);

UPDATE articles
SET editor_feedback = 'Good start! Please add more technical depth to the quantum entanglement section and include at least one real-world example of current quantum hardware (e.g. IBM Quantum, Google Sycamore).'
WHERE id = 5;

INSERT INTO article_tags (article_id, tag_id) VALUES
(1, 1),(1, 3),(2, 3),(3, 4),(4, 2),(4, 3),(5, 4);

INSERT INTO article_revisions (article_id, author_id, body_snapshot, saved_at) VALUES
(1, 2, '<p>First draft of MVC article...</p>',            NOW() - INTERVAL 12 DAY),
(1, 2, '<p>Revised MVC article with more examples...</p>', NOW() - INTERVAL 11 DAY),
(5, 2, '<p>First draft of quantum computing article...</p>', NOW() - INTERVAL 5 DAY);

INSERT INTO likes (article_id, user_id, created_at) VALUES
(1, 3, NOW() - INTERVAL 9 DAY),
(1, 4, NOW() - INTERVAL 8 DAY),
(1, 5, NOW() - INTERVAL 7 DAY),
(2, 3, NOW() - INTERVAL 6 DAY),
(2, 4, NOW() - INTERVAL 5 DAY);

INSERT INTO comments (id, article_id, user_id, body, created_at) VALUES
(1, 1, 3, 'Great article! Really helped me understand MVC.',              NOW() - INTERVAL 8 DAY),
(2, 1, 4, 'Could you write one about Laravel too?',                       NOW() - INTERVAL 7 DAY),
(3, 2, 5, 'The section on routing was very clear.',                       NOW() - INTERVAL 5 DAY),
(4, 2, 3, 'Would love to see a part 2 covering authentication!',          NOW() - INTERVAL 3 DAY);

INSERT INTO follows (follower_id, followed_author_id, created_at) VALUES
(3, 2, NOW() - INTERVAL 20 DAY),
(4, 2, NOW() - INTERVAL 15 DAY),
(5, 2, NOW() - INTERVAL 10 DAY),
(6, 2, NOW() - INTERVAL 5 DAY);

INSERT INTO editorial_calendar (article_id, editor_id, scheduled_date, note) VALUES
(3, 1, NOW() + INTERVAL 3 DAY, 'Schedule for Monday morning peak traffic slot.');

INSERT INTO reading_history (user_id, article_id, read_at) VALUES
(3, 1, NOW() - INTERVAL 9 DAY),
(4, 1, NOW() - INTERVAL 8 DAY),
(5, 1, NOW() - INTERVAL 7 DAY),
(3, 2, NOW() - INTERVAL 6 DAY),
(4, 2, NOW() - INTERVAL 5 DAY),
(6, 2, NOW() - INTERVAL 4 DAY);
