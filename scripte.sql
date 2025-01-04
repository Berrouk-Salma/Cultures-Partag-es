CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'author', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    is_published BOOLEAN DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);


-- Insert sample data into users
-- INSERT INTO users (name, email, password, role) 
-- VALUES 
--     ('Yasmine Khalil', 'yasmine@example.com', '$2y$10$encrypted1', 'admin'),
--     ('Kamal Rafi', 'kamal@example.com', '$2y$10$encrypted2', 'author'),
--     ('Leila Mokhtar', 'leila@example.com', '$2y$10$encrypted3', 'author'),
--     ('Hamza Tadili', 'hamza@example.com', '$2y$10$encrypted4', 'user'),
--     ('Nour Belkadi', 'nour@example.com', '$2y$10$encrypted5', 'user');

-- -- Insert sample categories with more diverse content
-- INSERT INTO categories (name, description) 
-- VALUES 
--     ('Tech Innovations', 'Exploring cutting-edge technological advancements and their impact'),
--     ('Career Growth', 'Professional development strategies and workplace success'),
--     ('Digital Art', 'Contemporary digital artistic expressions and techniques'),
--     ('Future Skills', 'Essential skills for the evolving professional landscape'),
--     ('Sustainable Living', 'Practical approaches to environmental consciousness');

-- -- Insert sample articles with varied status
-- INSERT INTO articles (title, content, user_id, category_id, is_published, published_at) 
-- VALUES 
--     ('The Rise of AI in Creative Industries', 
--      'Detailed exploration of how artificial intelligence is transforming creative processes...', 
--      2, 1, TRUE, CURRENT_TIMESTAMP),
--     ('Building a Sustainable Career Path', 
--      'Strategic approaches to long-term career development in the modern workplace...', 
--      3, 2, TRUE, CURRENT_TIMESTAMP),
--     ('Digital Art Revolution', 
--      'How digital tools are reshaping artistic expression and creativity...', 
--      2, 3, TRUE, CURRENT_TIMESTAMP),
--     ('Essential Skills for 2025', 
--      'Analysis of crucial competencies needed in the near future...', 
--      3, 4, FALSE, NULL),
--     ('Eco-Friendly Tech Solutions', 
--      'Innovative technological solutions for environmental challenges...', 
--      2, 5, TRUE, CURRENT_TIMESTAMP);

-- Advanced Select Queries

-- 1. Get trending articles with author info and engagement metrics
SELECT 
    a.title,
    u.name AS author,
    c.name AS category,
    DATE_FORMAT(a.published_at, '%d %M %Y') AS publish_date,
    CASE 
        WHEN a.is_published = TRUE THEN 'Live'
        ELSE 'Draft'
    END AS status
FROM articles a
INNER JOIN users u ON a.user_id = u.id
INNER JOIN categories c ON a.category_id = c.id
WHERE a.is_published = TRUE
ORDER BY a.published_at DESC;

-- 2. Author productivity analysis
SELECT 
    u.name AS author,
    COUNT(a.id) AS total_articles,
    SUM(CASE WHEN a.is_published = TRUE THEN 1 ELSE 0 END) AS published_articles,
    DATE_FORMAT(MAX(a.published_at), '%Y-%m-%d') AS last_published
FROM users u
LEFT JOIN articles a ON u.id = a.user_id
WHERE u.role = 'author'
GROUP BY u.id
ORDER BY published_articles DESC;

-- 3. Category performance overview
SELECT 
    c.name AS category,
    COUNT(a.id) AS total_articles,
    COUNT(DISTINCT a.user_id) AS unique_authors,
    DATE_FORMAT(MAX(a.published_at), '%Y-%m-%d') AS latest_article
FROM categories c
LEFT JOIN articles a ON c.id = a.category_id AND a.is_published = TRUE
GROUP BY c.id
ORDER BY total_articles DESC;

-- Create Views

-- 1. Latest published articles view
CREATE VIEW vw_latest_articles AS
SELECT 
    a.title,
    a.content,
    u.name AS author,
    c.name AS category,
    a.published_at,
    TIMESTAMPDIFF(DAY, a.published_at, CURRENT_TIMESTAMP) AS days_since_publish
FROM articles a
INNER JOIN users u ON a.user_id = u.id
INNER JOIN categories c ON a.category_id = c.id
WHERE a.is_published = TRUE
ORDER BY a.published_at DESC;

-- 2. Author dashboard view
CREATE VIEW vw_author_dashboard AS
SELECT 
    u.name AS author,
    u.email,
    COUNT(a.id) AS total_articles,
    SUM(CASE WHEN a.is_published = TRUE THEN 1 ELSE 0 END) AS published_count,
    SUM(CASE WHEN a.is_published = FALSE THEN 1 ELSE 0 END) AS draft_count,
    DATE_FORMAT(MAX(a.updated_at), '%Y-%m-%d %H:%i') AS last_update
FROM users u
LEFT JOIN articles a ON u.id = a.user_id
WHERE u.role = 'author'
GROUP BY u.id;