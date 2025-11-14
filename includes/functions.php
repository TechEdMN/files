<?php
session_start();

// Configuration
define('DATA_DIR', __DIR__ . '/../data');
define('CONFIG_FILE', DATA_DIR . '/config.json');
define('CATEGORIES_FILE', DATA_DIR . '/categories.json');
define('ARTICLES_FILE', DATA_DIR . '/articles.json');

// Helper Functions
function loadJSON($file) {
    if (!file_exists($file)) {
        return null;
    }
    $content = file_get_contents($file);
    return json_decode($content, true);
}

function saveJSON($file, $data) {
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function getConfig() {
    return loadJSON(CONFIG_FILE) ?: [];
}

function getCategories() {
    return loadJSON(CATEGORIES_FILE) ?: [];
}

function getArticles() {
    return loadJSON(ARTICLES_FILE) ?: [];
}

function saveCategories($categories) {
    return saveJSON(CATEGORIES_FILE, $categories);
}

function saveArticles($articles) {
    return saveJSON(ARTICLES_FILE, $articles);
}

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function generateId() {
    return uniqid('', true);
}

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

function getArticleById($id) {
    $articles = getArticles();
    foreach ($articles as $article) {
        if ($article['id'] === $id) {
            return $article;
        }
    }
    return null;
}

function getCategoryById($id) {
    $categories = getCategories();
    foreach ($categories as $category) {
        if ($category['id'] === $id) {
            return $category;
        }
    }
    return null;
}

function sanitizeInput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
