-- ========================================================
-- Pulse AI Blog Platform - Database Schema
-- Compatible with MySQL 5.7+ / 8.0+ / MariaDB 10.4+
-- ========================================================

CREATE DATABASE IF NOT EXISTS `ai_blog_app` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ai_blog_app`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'author', 'reader') DEFAULT 'author',
    `avatar` VARCHAR(255) DEFAULT 'https://ui-avatars.com/api/?name=User&background=4f46e5&color=fff&bold=true',
    `bio` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `slug` VARCHAR(60) NOT NULL UNIQUE,
    `description` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Posts Table (with AI fields, SEO metadata, reading time, view count)
CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `category_id` INT DEFAULT NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `content` LONGTEXT NOT NULL,
    `excerpt` TEXT DEFAULT NULL,
    `cover_image` VARCHAR(500) DEFAULT 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80',
    `ai_summary` TEXT DEFAULT NULL,
    `ai_tags` VARCHAR(255) DEFAULT NULL,
    `reading_time` INT DEFAULT 1,
    `views` INT DEFAULT 0,
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'published',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Comments Table
CREATE TABLE IF NOT EXISTS `comments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `post_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `content` TEXT NOT NULL,
    `likes` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Password Resets Table
CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_token` (`token`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- Seed Initial Sample Data (Admin user: admin@auralog.com / pass: admin123)
-- ========================================================

-- Default Admin & Author
INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `bio`) VALUES
(1, 'sithum_admin', 'admin@aurablog.com', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lR5eKq5v51qV8p0c/YVjA2pP3h1n2', 'admin', 'Lead Fullstack Developer & AI Enthusiast.'),
(2, 'tech_writer', 'writer@aurablog.com', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lR5eKq5v51qV8p0c/YVjA2pP3h1n2', 'author', 'Exploring the future of web engineering and GenAI.');

-- Default Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Artificial Intelligence', 'artificial-intelligence', 'Latest developments in Generative AI, LLMs, and prompt engineering.'),
(2, 'Web Development', 'web-development', 'PHP, JavaScript, Full-stack architecture, and cloud deployment.'),
(3, 'Career & Portfolio', 'career-portfolio', 'Tech interview guides, CV tips, and developer growth strategies.');

-- Default AI-Powered Sample Blog Post
INSERT INTO `posts` (`id`, `user_id`, `category_id`, `title`, `slug`, `content`, `excerpt`, `cover_image`, `ai_summary`, `ai_tags`, `reading_time`, `views`, `status`) VALUES
(1, 1, 1, 'Building Next-Gen Web Applications in the AI Era', 'building-next-gen-web-applications-in-the-ai-era', 
'# The Paradigm Shift in Web Development

Web application architecture is undergoing a foundational revolution. Where applications used to be passive data stores wrapped in HTML, the integration of **Large Language Models (LLMs)** like Gemini and modern agentic architectures has turned web platforms into intelligent, interactive assistants.

### Key Pillars of Modern AI Web Apps:
1. **Context-Aware Assistance**: Real-time writing copilot directly inside text editors.
2. **Instant Cognitive Relief**: Bulleted summaries synthesized from thousands of words in sub-second responses.
3. **Automated SEO & Taxonomy**: Natural language understanding that categorizes content without tedious manual tagging.

### Why Clean PHP is Still Relevant
Modern PHP 8+ provides superior memory efficiency, built-in JIT compilation, and rock-solid reliability. Paired with modern REST or serverless APIs, it delivers blazing-fast speeds at a fraction of cloud server costs.

*Start building the future today!*', 
'Discover how Generative AI and modern web design are revolutionizing content creation platforms in 2026.',
'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1200&q=80',
'• Web applications are evolving from passive storage into proactive AI-powered platforms.\n• Integrated LLMs provide real-time writing copilot and instant summaries.\n• Modern PHP 8+ continues to deliver unmatched performance and cost efficiency.',
'AI, WebDev, PHP, LLM, FutureTech',
3, 42, 'published');

-- Sample Comment
INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`) VALUES
(1, 1, 2, 'Incredible post! The integration of AI directly into the writing workflow makes blogging 10x faster.');
