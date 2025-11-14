<?php
require_once __DIR__ . '/../includes/functions.php';

// Check authentication
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_article':
        handleGetArticle();
        break;
    
    case 'create_article':
        handleCreateArticle();
        break;
    
    case 'update_article':
        handleUpdateArticle();
        break;
    
    case 'delete_article':
        handleDeleteArticle();
        break;
    
    case 'get_category':
        handleGetCategory();
        break;
    
    case 'create_category':
        handleCreateCategory();
        break;
    
    case 'update_category':
        handleUpdateCategory();
        break;
    
    case 'delete_category':
        handleDeleteCategory();
        break;
    
    case 'update_settings':
        handleUpdateSettings();
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

// ==========================================
// ARTICLE HANDLERS
// ==========================================

function handleGetArticle() {
    $id = $_GET['id'] ?? '';
    $article = getArticleById($id);
    
    if ($article) {
        echo json_encode(['success' => true, 'article' => $article]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Article not found']);
    }
}

function handleCreateArticle() {
    $title = $_POST['title'] ?? '';
    $categoryId = $_POST['category_id'] ?? '';
    $content = $_POST['content'] ?? '';
    
    if (empty($title) || empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Title and content are required']);
        return;
    }
    
    $articles = getArticles();
    
    $article = [
        'id' => generateId(),
        'title' => $title,
        'category_id' => $categoryId,
        'content' => $content,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => null
    ];
    
    $articles[] = $article;
    
    if (saveArticles($articles)) {
        echo json_encode(['success' => true, 'article' => $article]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save article']);
    }
}

function handleUpdateArticle() {
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'] ?? '';
    $categoryId = $_POST['category_id'] ?? '';
    $content = $_POST['content'] ?? '';
    
    if (empty($id) || empty($title) || empty($content)) {
        echo json_encode(['success' => false, 'message' => 'ID, title and content are required']);
        return;
    }
    
    $articles = getArticles();
    $found = false;
    
    foreach ($articles as &$article) {
        if ($article['id'] === $id) {
            $article['title'] = $title;
            $article['category_id'] = $categoryId;
            $article['content'] = $content;
            $article['updated_at'] = date('Y-m-d H:i:s');
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo json_encode(['success' => false, 'message' => 'Article not found']);
        return;
    }
    
    if (saveArticles($articles)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update article']);
    }
}

function handleDeleteArticle() {
    $id = $_POST['id'] ?? '';
    
    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'ID is required']);
        return;
    }
    
    $articles = getArticles();
    $filtered = array_filter($articles, function($article) use ($id) {
        return $article['id'] !== $id;
    });
    
    // Re-index array
    $filtered = array_values($filtered);
    
    if (saveArticles($filtered)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete article']);
    }
}

// ==========================================
// CATEGORY HANDLERS
// ==========================================

function handleGetCategory() {
    $id = $_GET['id'] ?? '';
    $category = getCategoryById($id);
    
    if ($category) {
        echo json_encode(['success' => true, 'category' => $category]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Category not found']);
    }
}

function handleCreateCategory() {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Name is required']);
        return;
    }
    
    $categories = getCategories();
    
    $category = [
        'id' => generateId(),
        'name' => $name,
        'description' => $description
    ];
    
    $categories[] = $category;
    
    if (saveCategories($categories)) {
        echo json_encode(['success' => true, 'category' => $category]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save category']);
    }
}

function handleUpdateCategory() {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if (empty($id) || empty($name)) {
        echo json_encode(['success' => false, 'message' => 'ID and name are required']);
        return;
    }
    
    $categories = getCategories();
    $found = false;
    
    foreach ($categories as &$category) {
        if ($category['id'] === $id) {
            $category['name'] = $name;
            $category['description'] = $description;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo json_encode(['success' => false, 'message' => 'Category not found']);
        return;
    }
    
    if (saveCategories($categories)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update category']);
    }
}

function handleDeleteCategory() {
    $id = $_POST['id'] ?? '';
    
    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'ID is required']);
        return;
    }
    
    $categories = getCategories();
    $filtered = array_filter($categories, function($category) use ($id) {
        return $category['id'] !== $id;
    });
    
    // Re-index array
    $filtered = array_values($filtered);
    
    if (saveCategories($filtered)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
    }
}

// ==========================================
// SETTINGS HANDLERS
// ==========================================

function handleUpdateSettings() {
    $siteName = $_POST['site_name'] ?? '';
    $siteDescription = $_POST['site_description'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    
    $config = getConfig();
    
    if ($siteName) {
        $config['site_name'] = $siteName;
    }
    
    if ($siteDescription) {
        $config['site_description'] = $siteDescription;
    }
    
    if ($newPassword) {
        $config['admin_password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
    }
    
    if (saveJSON(CONFIG_FILE, $config)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update settings']);
    }
}
