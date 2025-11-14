/* ==========================================
   ADMIN PANEL JAVASCRIPT
   ========================================== */

// Tab Management
function switchTab(tabName) {
    // Update nav items
    document.querySelectorAll('.admin-nav-item').forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('data-tab') === tabName) {
            item.classList.add('active');
        }
    });

    // Update tab content
    document.querySelectorAll('.admin-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    document.getElementById(`${tabName}-tab`).classList.add('active');
}

// Initialize tab switching
document.querySelectorAll('.admin-nav-item').forEach(item => {
    item.addEventListener('click', function() {
        const tab = this.getAttribute('data-tab');
        switchTab(tab);
    });
});

// ==========================================
// ARTICLE MANAGEMENT
// ==========================================

function showArticleForm(articleId = null) {
    const modal = document.getElementById('articleModal');
    const modalTitle = document.getElementById('articleModalTitle');
    const form = document.getElementById('articleForm');
    
    form.reset();
    document.getElementById('article_id').value = '';
    
    if (articleId) {
        modalTitle.textContent = 'Edit Article';
        loadArticle(articleId);
    } else {
        modalTitle.textContent = 'New Article';
    }
    
    modal.classList.remove('hidden');
}

function closeArticleModal() {
    const modal = document.getElementById('articleModal');
    modal.classList.add('hidden');
}

function loadArticle(articleId) {
    fetch(`api.php?action=get_article&id=${articleId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const article = data.article;
                document.getElementById('article_id').value = article.id;
                document.getElementById('article_title').value = article.title;
                document.getElementById('article_category').value = article.category_id || '';
                document.getElementById('article_content').value = article.content;
            } else {
                showNotification('Error loading article', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error loading article', 'error');
        });
}

function editArticle(articleId) {
    showArticleForm(articleId);
}

function deleteArticle(articleId) {
    if (!confirm('Are you sure you want to delete this article?')) {
        return;
    }
    
    fetch('api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=delete_article&id=${articleId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Article deleted successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error deleting article', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error deleting article', 'error');
    });
}

// Handle article form submission
document.getElementById('articleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const articleId = document.getElementById('article_id').value;
    const title = document.getElementById('article_title').value;
    const category = document.getElementById('article_category').value;
    const content = document.getElementById('article_content').value;
    
    const formData = new URLSearchParams();
    formData.append('action', articleId ? 'update_article' : 'create_article');
    if (articleId) formData.append('id', articleId);
    formData.append('title', title);
    formData.append('category_id', category);
    formData.append('content', content);
    
    fetch('api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(articleId ? 'Article updated successfully' : 'Article created successfully', 'success');
            closeArticleModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error saving article', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving article', 'error');
    });
});

// ==========================================
// CATEGORY MANAGEMENT
// ==========================================

function showCategoryForm(categoryId = null) {
    const modal = document.getElementById('categoryModal');
    const modalTitle = document.getElementById('categoryModalTitle');
    const form = document.getElementById('categoryForm');
    
    form.reset();
    document.getElementById('category_id').value = '';
    
    if (categoryId) {
        modalTitle.textContent = 'Edit Category';
        loadCategory(categoryId);
    } else {
        modalTitle.textContent = 'New Category';
    }
    
    modal.classList.remove('hidden');
}

function closeCategoryModal() {
    const modal = document.getElementById('categoryModal');
    modal.classList.add('hidden');
}

function loadCategory(categoryId) {
    fetch(`api.php?action=get_category&id=${categoryId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const category = data.category;
                document.getElementById('category_id').value = category.id;
                document.getElementById('category_name').value = category.name;
                document.getElementById('category_description').value = category.description || '';
            } else {
                showNotification('Error loading category', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error loading category', 'error');
        });
}

function editCategory(categoryId) {
    showCategoryForm(categoryId);
}

function deleteCategory(categoryId) {
    if (!confirm('Are you sure you want to delete this category? Articles in this category will become uncategorized.')) {
        return;
    }
    
    fetch('api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=delete_category&id=${categoryId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Category deleted successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error deleting category', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error deleting category', 'error');
    });
}

// Handle category form submission
document.getElementById('categoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const categoryId = document.getElementById('category_id').value;
    const name = document.getElementById('category_name').value;
    const description = document.getElementById('category_description').value;
    
    const formData = new URLSearchParams();
    formData.append('action', categoryId ? 'update_category' : 'create_category');
    if (categoryId) formData.append('id', categoryId);
    formData.append('name', name);
    formData.append('description', description);
    
    fetch('api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(categoryId ? 'Category updated successfully' : 'Category created successfully', 'success');
            closeCategoryModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error saving category', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving category', 'error');
    });
});

// ==========================================
// SETTINGS MANAGEMENT
// ==========================================

function saveSettings() {
    const siteName = document.getElementById('site_name').value;
    const siteDescription = document.getElementById('site_description').value;
    const newPassword = document.getElementById('new_password').value;
    
    const formData = new URLSearchParams();
    formData.append('action', 'update_settings');
    formData.append('site_name', siteName);
    formData.append('site_description', siteDescription);
    if (newPassword) {
        formData.append('new_password', newPassword);
    }
    
    fetch('api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Settings updated successfully', 'success');
            if (newPassword) {
                document.getElementById('new_password').value = '';
            }
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error saving settings', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving settings', 'error');
    });
}

// ==========================================
// NOTIFICATIONS
// ==========================================

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelector('.notification');
    if (existing) {
        existing.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 90px;
        right: 20px;
        padding: 1rem 1.5rem;
        background-color: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        border-radius: var(--border-radius);
        box-shadow: 0 10px 25px var(--shadow-color);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
    `;
    
    const icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
    notification.innerHTML = `
        <i class="fas ${icon}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// ==========================================
// MODAL CLOSE HANDLERS
// ==========================================

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function() {
        this.parentElement.classList.add('hidden');
    });
});

// ESC key to close modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.add('hidden');
        });
    }
});

console.log('Admin panel initialized successfully!');
