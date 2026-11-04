<?php
/**
 * SQLite Database Auto-initializer
 * Ensures the application runs 100% out of the box with zero database configuration.
 */

$dbPath = __DIR__ . '/blog.sqlite';
$pdo = new PDO("sqlite:" . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Users Table
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT DEFAULT 'author',
    avatar TEXT DEFAULT 'https://ui-avatars.com/api/?name=User&background=4f46e5&color=fff&bold=true',
    bio TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// 2. Categories Table
$pdo->exec("CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    slug TEXT NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// 3. Posts Table
$pdo->exec("CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    category_id INTEGER DEFAULT NULL,
    title TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    content TEXT NOT NULL,
    excerpt TEXT DEFAULT NULL,
    cover_image TEXT DEFAULT 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80',
    ai_summary TEXT DEFAULT NULL,
    ai_tags TEXT DEFAULT NULL,
    reading_time INTEGER DEFAULT 1,
    views INTEGER DEFAULT 0,
    status TEXT DEFAULT 'published',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
)");

// 4. Comments Table
$pdo->exec("CREATE TABLE IF NOT EXISTS comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    post_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    content TEXT NOT NULL,
    likes INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// 5. Password Resets Table
$pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL,
    token TEXT NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Seed default users if empty
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
if ($userCount == 0) {
    $stmt = $pdo->prepare("INSERT INTO users (id, username, email, password, role, bio) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        1, 
        'sithum_admin', 
        'admin@aurablog.com', 
        password_hash('admin123', PASSWORD_BCRYPT), 
        'admin', 
        'Lead Fullstack Developer & AI Enthusiast.'
    ]);
    $stmt->execute([
        2, 
        'tech_writer', 
        'writer@aurablog.com', 
        password_hash('admin123', PASSWORD_BCRYPT), 
        'author', 
        'Exploring the future of web engineering and GenAI.'
    ]);
}

// Seed default categories if empty
$catCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
if ($catCount == 0) {
    $stmt = $pdo->prepare("INSERT INTO categories (id, name, slug, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([1, 'Artificial Intelligence', 'artificial-intelligence', 'Latest developments in Generative AI, LLMs, and prompt engineering.']);
    $stmt->execute([2, 'Web Development', 'web-development', 'PHP, JavaScript, Full-stack architecture, and cloud deployment.']);
    $stmt->execute([3, 'Career & Portfolio', 'career-portfolio', 'Tech interview guides, CV tips, and developer growth strategies.']);
}

// Seed default post if empty
$postCount = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
if ($postCount == 0) {
    $content = "# The Paradigm Shift in Web Development\n\nWeb application architecture is undergoing a foundational revolution. Where applications used to be passive data stores wrapped in HTML, the integration of **Large Language Models (LLMs)** like Gemini and modern agentic architectures has turned web platforms into intelligent, interactive assistants.\n\n### Key Pillars of Modern AI Web Apps:\n1. **Context-Aware Assistance**: Real-time writing copilot directly inside text editors.\n2. **Instant Cognitive Relief**: Bulleted summaries synthesized from thousands of words in sub-second responses.\n3. **Automated SEO & Taxonomy**: Natural language understanding that categorizes content without tedious manual tagging.\n\n### Why Clean PHP is Still Relevant\nModern PHP 8+ provides superior memory efficiency, built-in JIT compilation, and rock-solid reliability. Paired with modern REST or serverless APIs, it delivers blazing-fast speeds at a fraction of cloud server costs.\n\n*Start building the future today!*";
    
    $stmt = $pdo->prepare("INSERT INTO posts (id, user_id, category_id, title, slug, content, excerpt, cover_image, ai_summary, ai_tags, reading_time, views, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        1, 1, 1,
        'Building Next-Gen Web Applications in the AI Era',
        'building-next-gen-web-applications-in-the-ai-era',
        $content,
        'Discover how Generative AI and modern web design are revolutionizing content creation platforms in 2026.',
        'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1200&q=80',
        "• Web applications are evolving from passive storage into proactive AI-powered platforms.\n• Integrated LLMs provide real-time writing copilot and instant summaries.\n• Modern PHP 8+ continues to deliver unmatched performance and cost efficiency.",
        'AI, WebDev, PHP, LLM, FutureTech',
        3, 42, 'published'
    ]);

    // Sample comment
    $stmt = $pdo->prepare("INSERT INTO comments (id, post_id, user_id, content) VALUES (?, ?, ?, ?)");
    $stmt->execute([1, 1, 2, 'Incredible post! The integration of AI directly into the writing workflow makes blogging 10x faster.']);
}

echo "SUCCESS: SQLite database initialized with tables and sample data.\n";
