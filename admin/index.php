<?php
require_once __DIR__ . '/../includes/functions.php';

// Check authentication
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: login.php');
    exit;
}

$config = getConfig();
$categories = getCategories();
$articles = getArticles();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo sanitizeInput($config['site_name'] ?? 'Documentation'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="container-custom flex items-center justify-between py-4">
            <div class="flex items-center space-x-4">
                <h1 class="text-xl font-bold gradient-text">
                    <i class="fas fa-cog"></i> Admin Panel
                </h1>
            </div>
            
            <div class="flex items-center space-x-4">
                <a href="../" class="btn-secondary" target="_blank">
                    <i class="fas fa-eye"></i> View Site
                </a>
                <a href="?action=logout" class="btn-secondary">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <div class="admin-layout">
        <!-- Admin Sidebar -->
        <aside class="admin-sidebar">
            <nav class="admin-nav">
                <button class="admin-nav-item active" data-tab="dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </button>
                <button class="admin-nav-item" data-tab="articles">
                    <i class="fas fa-file-alt"></i>
                    <span>Articles</span>
                </button>
                <button class="admin-nav-item" data-tab="categories">
                    <i class="fas fa-folder"></i>
                    <span>Categories</span>
                </button>
                <button class="admin-nav-item" data-tab="settings">
                    <i class="fas fa-sliders-h"></i>
                    <span>Settings</span>
                </button>
            </nav>
        </aside>

        <!-- Admin Content -->
        <main class="admin-content">
            <!-- Dashboard Tab -->
            <div class="admin-tab active" id="dashboard-tab">
                <div class="admin-header-section">
                    <h2>Dashboard</h2>
                    <p>Overview of your documentation system</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: #3b82f6;">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value"><?php echo count($articles); ?></div>
                            <div class="stat-label">Total Articles</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: #10b981;">
                            <i class="fas fa-folder"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value"><?php echo count($categories); ?></div>
                            <div class="stat-label">Categories</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: #f59e0b;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value">
                                <?php 
                                $recent = array_filter($articles, function($a) {
                                    return isset($a['created_at']) && strtotime($a['created_at']) > strtotime('-7 days');
                                });
                                echo count($recent);
                                ?>
                            </div>
                            <div class="stat-label">Recent (7 days)</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: #8b5cf6;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value">
                                <?php 
                                $published = array_filter($articles, function($a) {
                                    return !isset($a['draft']) || !$a['draft'];
                                });
                                echo count($published);
                                ?>
                            </div>
                            <div class="stat-label">Published</div>
                        </div>
                    </div>
                </div>

                <div class="quick-actions">
                    <h3>Quick Actions</h3>
                    <div class="action-buttons">
                        <button class="action-btn" onclick="switchTab('articles'); showArticleForm();">
                            <i class="fas fa-plus"></i>
                            Create New Article
                        </button>
                        <button class="action-btn" onclick="switchTab('categories'); showCategoryForm();">
                            <i class="fas fa-folder-plus"></i>
                            Add Category
                        </button>
                    </div>
                </div>
            </div>

            <!-- Articles Tab -->
            <div class="admin-tab" id="articles-tab">
                <div class="admin-header-section">
                    <h2>Articles</h2>
                    <button class="btn-primary" onclick="showArticleForm()">
                        <i class="fas fa-plus"></i> New Article
                    </button>
                </div>

                <div id="articlesContainer">
                    <?php if (empty($articles)): ?>
                        <div class="empty-state-admin">
                            <i class="fas fa-file-alt"></i>
                            <h3>No articles yet</h3>
                            <p>Create your first article to get started</p>
                            <button class="btn-primary" onclick="showArticleForm()">
                                <i class="fas fa-plus"></i> Create Article
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="articles-list">
                            <?php foreach ($articles as $article): ?>
                                <div class="article-item" data-id="<?php echo $article['id']; ?>">
                                    <div class="article-item-header">
                                        <h3><?php echo sanitizeInput($article['title']); ?></h3>
                                        <div class="article-item-meta">
                                            <span class="badge">
                                                <?php 
                                                $cat = getCategoryById($article['category_id'] ?? '');
                                                echo $cat ? sanitizeInput($cat['name']) : 'Uncategorized';
                                                ?>
                                            </span>
                                            <span class="date">
                                                <i class="fas fa-calendar"></i>
                                                <?php echo date('M j, Y', strtotime($article['created_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="article-item-actions">
                                        <button class="btn-icon" onclick="editArticle('<?php echo $article['id']; ?>')" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon" onclick="deleteArticle('<?php echo $article['id']; ?>')" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Categories Tab -->
            <div class="admin-tab" id="categories-tab">
                <div class="admin-header-section">
                    <h2>Categories</h2>
                    <button class="btn-primary" onclick="showCategoryForm()">
                        <i class="fas fa-plus"></i> New Category
                    </button>
                </div>

                <div id="categoriesContainer">
                    <?php if (empty($categories)): ?>
                        <div class="empty-state-admin">
                            <i class="fas fa-folder"></i>
                            <h3>No categories yet</h3>
                            <p>Create your first category to organize articles</p>
                            <button class="btn-primary" onclick="showCategoryForm()">
                                <i class="fas fa-plus"></i> Create Category
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="categories-grid">
                            <?php foreach ($categories as $category): ?>
                                <div class="category-card" data-id="<?php echo $category['id']; ?>">
                                    <div class="category-icon">
                                        <i class="fas fa-folder"></i>
                                    </div>
                                    <h3><?php echo sanitizeInput($category['name']); ?></h3>
                                    <?php if (isset($category['description'])): ?>
                                        <p><?php echo sanitizeInput($category['description']); ?></p>
                                    <?php endif; ?>
                                    <div class="category-actions">
                                        <button class="btn-icon" onclick="editCategory('<?php echo $category['id']; ?>')" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon" onclick="deleteCategory('<?php echo $category['id']; ?>')" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Settings Tab -->
            <div class="admin-tab" id="settings-tab">
                <div class="admin-header-section">
                    <h2>Settings</h2>
                    <button class="btn-primary" onclick="saveSettings()">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>

                <div class="settings-form">
                    <div class="form-group">
                        <label for="site_name">Site Name</label>
                        <input type="text" id="site_name" value="<?php echo sanitizeInput($config['site_name'] ?? ''); ?>" placeholder="Documentation Hub">
                    </div>

                    <div class="form-group">
                        <label for="site_description">Site Description</label>
                        <textarea id="site_description" rows="3" placeholder="A modern documentation system"><?php echo sanitizeInput($config['site_description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="new_password">Change Admin Password</label>
                        <input type="password" id="new_password" placeholder="Leave empty to keep current password">
                    </div>

                    <div class="alert-info">
                        <i class="fas fa-info-circle"></i>
                        Settings are saved to <code>data/config.json</code>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Article Form Modal -->
    <div id="articleModal" class="modal hidden">
        <div class="modal-overlay"></div>
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3 id="articleModalTitle">New Article</h3>
                <button class="modal-close" onclick="closeArticleModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="articleForm">
                    <input type="hidden" id="article_id" value="">
                    
                    <div class="form-group">
                        <label for="article_title">Title *</label>
                        <input type="text" id="article_title" required placeholder="Article Title">
                    </div>

                    <div class="form-group">
                        <label for="article_category">Category</label>
                        <select id="article_category">
                            <option value="">Uncategorized</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo sanitizeInput($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="article_content">Content (Markdown) *</label>
                        <textarea id="article_content" rows="15" required placeholder="# Heading 1&#10;&#10;Your content here in **markdown** format..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="closeArticleModal()">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Save Article
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Category Form Modal -->
    <div id="categoryModal" class="modal hidden">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="categoryModalTitle">New Category</h3>
                <button class="modal-close" onclick="closeCategoryModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" id="category_id" value="">
                    
                    <div class="form-group">
                        <label for="category_name">Name *</label>
                        <input type="text" id="category_name" required placeholder="Category Name">
                    </div>

                    <div class="form-group">
                        <label for="category_description">Description</label>
                        <textarea id="category_description" rows="3" placeholder="Optional description"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="closeCategoryModal()">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="admin.js"></script>
</body>
</html>
