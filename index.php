<?php
require_once __DIR__ . '/includes/functions.php';

$config = getConfig();
$categories = getCategories();
$articles = getArticles();

// Get current article from URL
$currentArticleId = isset($_GET['article']) ? $_GET['article'] : null;
$currentArticle = $currentArticleId ? getArticleById($currentArticleId) : null;

// Get section anchor from URL
$sectionAnchor = isset($_GET['section']) ? $_GET['section'] : null;

// Default to first article if none selected
if (!$currentArticle && !empty($articles)) {
    $currentArticle = $articles[0];
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitizeInput($config['site_name'] ?? 'Documentation'); ?></title>
    <meta name="description" content="<?php echo sanitizeInput($config['site_description'] ?? ''); ?>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Markdown Parser -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    
    <!-- Prism.js for code highlighting -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-bash.min.js"></script>
</head>
<body class="antialiased">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-40 header-blur">
        <div class="container-custom flex items-center justify-between py-4">
            <div class="flex items-center space-x-4">
                <button id="sidebarToggle" class="lg:hidden btn-icon" aria-label="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="text-xl font-bold gradient-text"><?php echo sanitizeInput($config['site_name'] ?? 'Documentation'); ?></h1>
            </div>
            
            <div class="flex items-center space-x-4">
                <!-- Search Button -->
                <button id="searchBtn" class="btn-icon" aria-label="Search">
                    <i class="fas fa-search"></i>
                </button>
                
                <!-- Theme Switcher -->
                <div class="relative theme-switcher">
                    <button id="themeBtn" class="btn-icon" aria-label="Change Theme">
                        <i class="fas fa-palette"></i>
                    </button>
                    <div id="themeMenu" class="theme-menu hidden">
                        <button class="theme-option" data-theme="dark">
                            <i class="fas fa-moon"></i> Dark
                        </button>
                        <button class="theme-option" data-theme="light">
                            <i class="fas fa-sun"></i> Light
                        </button>
                        <button class="theme-option" data-theme="ocean">
                            <i class="fas fa-water"></i> Ocean
                        </button>
                        <button class="theme-option" data-theme="forest">
                            <i class="fas fa-tree"></i> Forest
                        </button>
                        <button class="theme-option" data-theme="sunset">
                            <i class="fas fa-cloud-sun"></i> Sunset
                        </button>
                        <button class="theme-option" data-theme="neon">
                            <i class="fas fa-bolt"></i> Neon
                        </button>
                        <button class="theme-option" data-theme="midnight">
                            <i class="fas fa-star"></i> Midnight
                        </button>
                        <button class="theme-option" data-theme="spring">
                            <i class="fas fa-leaf"></i> Spring
                        </button>
                    </div>
                </div>
                
                <!-- Admin Link -->
                <a href="admin/" class="btn-icon" aria-label="Admin Panel">
                    <i class="fas fa-cog"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Search Modal -->
    <div id="searchModal" class="modal hidden">
        <div class="modal-overlay"></div>
        <div class="modal-content search-modal">
            <div class="modal-header">
                <h3 class="text-xl font-bold">Search Documentation</h3>
                <button class="modal-close" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchInput" placeholder="Type to search..." class="search-input" autofocus>
                <div id="searchResults" class="search-results"></div>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div id="shareModal" class="modal hidden">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-xl font-bold">Share This Article</h3>
                <button class="modal-close" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="share-options">
                    <div class="form-group">
                        <label>Article Link</label>
                        <div class="input-group">
                            <input type="text" id="shareArticleLink" readonly class="share-link-input">
                            <button class="btn-primary" onclick="copyToClipboard('shareArticleLink')">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    <div class="form-group" id="shareSectionGroup" style="display: none;">
                        <label>Section Link</label>
                        <div class="input-group">
                            <input type="text" id="shareSectionLink" readonly class="share-link-input">
                            <button class="btn-primary" onclick="copyToClipboard('shareSectionLink')">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar">
            <div class="sidebar-content">
                <nav class="sidebar-nav">
                    <?php
                    // Group articles by category
                    $articlesByCategory = [];
                    foreach ($articles as $article) {
                        $catId = $article['category_id'] ?? 'uncategorized';
                        if (!isset($articlesByCategory[$catId])) {
                            $articlesByCategory[$catId] = [];
                        }
                        $articlesByCategory[$catId][] = $article;
                    }

                    // Display categories and articles
                    if (!empty($categories)) {
                        foreach ($categories as $category) {
                            $catArticles = $articlesByCategory[$category['id']] ?? [];
                            if (empty($catArticles)) continue;
                            
                            echo '<div class="sidebar-category">';
                            echo '<div class="sidebar-category-header">';
                            echo '<i class="fas fa-folder"></i>';
                            echo '<span>' . sanitizeInput($category['name']) . '</span>';
                            echo '<i class="fas fa-chevron-down category-toggle"></i>';
                            echo '</div>';
                            echo '<div class="sidebar-category-items">';
                            
                            foreach ($catArticles as $article) {
                                $isActive = $currentArticle && $currentArticle['id'] === $article['id'];
                                $activeClass = $isActive ? 'active' : '';
                                echo '<a href="?article=' . urlencode($article['id']) . '" class="sidebar-item ' . $activeClass . '">';
                                echo '<i class="fas fa-file-alt"></i>';
                                echo '<span>' . sanitizeInput($article['title']) . '</span>';
                                echo '</a>';
                            }
                            
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    
                    // Uncategorized articles
                    $uncategorized = $articlesByCategory['uncategorized'] ?? [];
                    if (!empty($uncategorized)) {
                        echo '<div class="sidebar-category">';
                        echo '<div class="sidebar-category-header">';
                        echo '<i class="fas fa-file"></i>';
                        echo '<span>Uncategorized</span>';
                        echo '<i class="fas fa-chevron-down category-toggle"></i>';
                        echo '</div>';
                        echo '<div class="sidebar-category-items">';
                        
                        foreach ($uncategorized as $article) {
                            $isActive = $currentArticle && $currentArticle['id'] === $article['id'];
                            $activeClass = $isActive ? 'active' : '';
                            echo '<a href="?article=' . urlencode($article['id']) . '" class="sidebar-item ' . $activeClass . '">';
                            echo '<i class="fas fa-file-alt"></i>';
                            echo '<span>' . sanitizeInput($article['title']) . '</span>';
                            echo '</a>';
                        }
                        
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="content-area">
            <?php if ($currentArticle): ?>
                <article class="article-content">
                    <div class="article-header">
                        <h1 class="article-title"><?php echo sanitizeInput($currentArticle['title']); ?></h1>
                        <div class="article-meta">
                            <span class="article-date">
                                <i class="fas fa-calendar"></i>
                                <?php echo date('F j, Y', strtotime($currentArticle['created_at'])); ?>
                            </span>
                            <?php if (isset($currentArticle['updated_at']) && $currentArticle['updated_at']): ?>
                                <span class="article-updated">
                                    <i class="fas fa-sync"></i>
                                    Updated: <?php echo date('F j, Y', strtotime($currentArticle['updated_at'])); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <button id="shareBtn" class="btn-secondary">
                            <i class="fas fa-share-alt"></i> Share
                        </button>
                    </div>
                    
                    <div class="article-body markdown-content" id="articleContent">
                        <?php echo $currentArticle['content'] ?? ''; ?>
                    </div>
                </article>
                
                <!-- Table of Contents (floating) -->
                <aside class="toc-sidebar" id="tocSidebar">
                    <div class="toc-header">
                        <h3>On This Page</h3>
                    </div>
                    <nav class="toc-nav" id="tocNav"></nav>
                </aside>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <h2>Welcome to the Documentation</h2>
                    <p>No articles available yet. Visit the <a href="admin/">admin panel</a> to create your first article.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Custom Scripts -->
    <script src="assets/js/main.js"></script>
    
    <script>
        // Pass PHP data to JavaScript
        window.currentArticle = <?php echo json_encode($currentArticle); ?>;
        window.allArticles = <?php echo json_encode($articles); ?>;
        window.sectionAnchor = <?php echo json_encode($sectionAnchor); ?>;
    </script>
</body>
</html>
