/* ==========================================
   DOCUMENTATION SYSTEM - MAIN JAVASCRIPT
   Modern, Interactive Features
   ========================================== */

// ==========================================
// INITIALIZATION
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    initializeTheme();
    initializeSidebar();
    initializeMarkdown();
    initializeTableOfContents();
    initializeSearch();
    initializeShare();
    initializeScrollAnimations();
    initializeCopyButtons();
    
    // Scroll to section if specified in URL
    if (window.sectionAnchor) {
        setTimeout(() => {
            const element = document.getElementById(window.sectionAnchor);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth' });
            }
        }, 500);
    }
});

// ==========================================
// THEME SYSTEM
// ==========================================

function initializeTheme() {
    const themeBtn = document.getElementById('themeBtn');
    const themeMenu = document.getElementById('themeMenu');
    const themeOptions = document.querySelectorAll('.theme-option');
    
    // Load saved theme
    const savedTheme = localStorage.getItem('theme') || 'dark';
    setTheme(savedTheme);
    
    // Toggle theme menu
    themeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        themeMenu.classList.toggle('hidden');
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!themeMenu.classList.contains('hidden') && 
            !themeMenu.contains(e.target) && 
            e.target !== themeBtn) {
            themeMenu.classList.add('hidden');
        }
    });
    
    // Theme selection
    themeOptions.forEach(option => {
        option.addEventListener('click', function() {
            const theme = this.getAttribute('data-theme');
            setTheme(theme);
            themeMenu.classList.add('hidden');
        });
    });
}

function setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    
    // Add theme change animation
    document.body.style.transition = 'background-color 0.5s ease, color 0.5s ease';
}

// ==========================================
// SIDEBAR
// ==========================================

function initializeSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    
    // Toggle sidebar on mobile
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
    
    // Category collapse/expand
    const categoryHeaders = document.querySelectorAll('.sidebar-category-header');
    categoryHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const category = this.parentElement;
            category.classList.toggle('collapsed');
        });
    });
    
    // Close sidebar on mobile when clicking a link
    const sidebarLinks = document.querySelectorAll('.sidebar-item');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 1024) {
                sidebar.classList.remove('active');
            }
        });
    });
}

// ==========================================
// MARKDOWN RENDERING
// ==========================================

function initializeMarkdown() {
    const contentElement = document.getElementById('articleContent');
    
    if (contentElement && window.currentArticle) {
        const markdownContent = window.currentArticle.content || '';
        
        // Configure marked
        marked.setOptions({
            highlight: function(code, lang) {
                if (lang && Prism.languages[lang]) {
                    return Prism.highlight(code, Prism.languages[lang], lang);
                }
                return code;
            },
            breaks: true,
            gfm: true
        });
        
        // Render markdown
        const html = marked.parse(markdownContent);
        contentElement.innerHTML = html;
        
        // Add IDs to headings for linking
        const headings = contentElement.querySelectorAll('h1, h2, h3, h4, h5, h6');
        headings.forEach((heading, index) => {
            const text = heading.textContent;
            const id = slugify(text) || `heading-${index}`;
            heading.id = id;
            
            // Add anchor link
            const anchor = document.createElement('a');
            anchor.className = 'heading-anchor';
            anchor.href = `#${id}`;
            anchor.innerHTML = '<i class="fas fa-link"></i>';
            anchor.style.marginLeft = '0.5rem';
            anchor.style.opacity = '0';
            anchor.style.transition = 'opacity 0.3s ease';
            anchor.style.fontSize = '0.7em';
            anchor.style.color = 'var(--accent-primary)';
            heading.appendChild(anchor);
            
            heading.addEventListener('mouseenter', function() {
                anchor.style.opacity = '1';
            });
            
            heading.addEventListener('mouseleave', function() {
                anchor.style.opacity = '0';
            });
        });
        
        // Highlight code blocks
        Prism.highlightAllUnder(contentElement);
    }
}

function slugify(text) {
    return text
        .toString()
        .toLowerCase()
        .trim()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '');
}

// ==========================================
// TABLE OF CONTENTS
// ==========================================

function initializeTableOfContents() {
    const tocNav = document.getElementById('tocNav');
    const contentElement = document.getElementById('articleContent');
    
    if (!tocNav || !contentElement || !window.currentArticle) {
        const tocSidebar = document.getElementById('tocSidebar');
        if (tocSidebar) tocSidebar.style.display = 'none';
        return;
    }
    
    const headings = contentElement.querySelectorAll('h2, h3, h4');
    
    if (headings.length === 0) {
        const tocSidebar = document.getElementById('tocSidebar');
        if (tocSidebar) tocSidebar.style.display = 'none';
        return;
    }
    
    tocNav.innerHTML = '';
    
    headings.forEach(heading => {
        const link = document.createElement('a');
        link.href = `#${heading.id}`;
        link.textContent = heading.textContent.replace(/\s*$/, '');
        link.className = `toc-link level-${heading.tagName.toLowerCase().replace('h', '')}`;
        
        link.addEventListener('click', function(e) {
            e.preventDefault();
            heading.scrollIntoView({ behavior: 'smooth' });
            history.pushState(null, null, `#${heading.id}`);
        });
        
        tocNav.appendChild(link);
    });
    
    // Highlight active section on scroll
    let ticking = false;
    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                updateActiveTocLink();
                ticking = false;
            });
            ticking = true;
        }
    });
}

function updateActiveTocLink() {
    const tocLinks = document.querySelectorAll('.toc-link');
    const headings = document.querySelectorAll('#articleContent h2, #articleContent h3, #articleContent h4');
    
    let activeHeading = null;
    
    headings.forEach(heading => {
        const rect = heading.getBoundingClientRect();
        if (rect.top <= 150) {
            activeHeading = heading;
        }
    });
    
    tocLinks.forEach(link => {
        link.classList.remove('active');
        if (activeHeading && link.href.includes(`#${activeHeading.id}`)) {
            link.classList.add('active');
        }
    });
}

// ==========================================
// SEARCH FUNCTIONALITY
// ==========================================

function initializeSearch() {
    const searchBtn = document.getElementById('searchBtn');
    const searchModal = document.getElementById('searchModal');
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const modalClose = searchModal.querySelector('.modal-close');
    const modalOverlay = searchModal.querySelector('.modal-overlay');
    
    // Open search modal
    searchBtn.addEventListener('click', function() {
        searchModal.classList.remove('hidden');
        searchInput.focus();
    });
    
    // Close search modal
    modalClose.addEventListener('click', closeSearchModal);
    modalOverlay.addEventListener('click', closeSearchModal);
    
    // ESC key to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !searchModal.classList.contains('hidden')) {
            closeSearchModal();
        }
    });
    
    // Search input
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch(this.value);
        }, 300);
    });
    
    function closeSearchModal() {
        searchModal.classList.add('hidden');
        searchInput.value = '';
        searchResults.innerHTML = '';
    }
    
    function performSearch(query) {
        if (!query || query.length < 2) {
            searchResults.innerHTML = '<p style="color: var(--text-muted); text-align: center; padding: 2rem;">Type at least 2 characters to search...</p>';
            return;
        }
        
        const results = [];
        const lowerQuery = query.toLowerCase();
        
        if (window.allArticles) {
            window.allArticles.forEach(article => {
                const title = article.title.toLowerCase();
                const content = article.content.toLowerCase();
                
                if (title.includes(lowerQuery) || content.includes(lowerQuery)) {
                    // Get excerpt
                    const index = content.indexOf(lowerQuery);
                    const start = Math.max(0, index - 50);
                    const end = Math.min(content.length, index + 100);
                    let excerpt = article.content.substring(start, end);
                    
                    if (start > 0) excerpt = '...' + excerpt;
                    if (end < content.length) excerpt = excerpt + '...';
                    
                    // Highlight search term
                    const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
                    const highlightedTitle = article.title.replace(regex, '<span class="search-highlight">$1</span>');
                    const highlightedExcerpt = excerpt.replace(regex, '<span class="search-highlight">$1</span>');
                    
                    results.push({
                        id: article.id,
                        title: highlightedTitle,
                        excerpt: highlightedExcerpt
                    });
                }
            });
        }
        
        displaySearchResults(results);
    }
    
    function displaySearchResults(results) {
        if (results.length === 0) {
            searchResults.innerHTML = '<p style="color: var(--text-muted); text-align: center; padding: 2rem;">No results found.</p>';
            return;
        }
        
        searchResults.innerHTML = results.map(result => `
            <div class="search-result-item" onclick="navigateToArticle('${result.id}')">
                <div class="search-result-title">${result.title}</div>
                <div class="search-result-excerpt">${result.excerpt}</div>
            </div>
        `).join('');
    }
    
    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
}

function navigateToArticle(articleId) {
    window.location.href = `?article=${articleId}`;
}

// ==========================================
// SHARE FUNCTIONALITY
// ==========================================

function initializeShare() {
    const shareBtn = document.getElementById('shareBtn');
    const shareModal = document.getElementById('shareModal');
    
    if (!shareBtn || !shareModal) return;
    
    const modalClose = shareModal.querySelector('.modal-close');
    const modalOverlay = shareModal.querySelector('.modal-overlay');
    const articleLinkInput = document.getElementById('shareArticleLink');
    const sectionLinkInput = document.getElementById('shareSectionLink');
    const shareSectionGroup = document.getElementById('shareSectionGroup');
    
    // Open share modal
    shareBtn.addEventListener('click', function() {
        if (!window.currentArticle) return;
        
        // Generate article link
        const articleUrl = `${window.location.origin}${window.location.pathname}?article=${window.currentArticle.id}`;
        articleLinkInput.value = articleUrl;
        
        // Check if we're on a specific section
        const hash = window.location.hash;
        if (hash) {
            const sectionUrl = `${articleUrl}&section=${hash.substring(1)}`;
            sectionLinkInput.value = sectionUrl;
            shareSectionGroup.style.display = 'block';
        } else {
            shareSectionGroup.style.display = 'none';
        }
        
        shareModal.classList.remove('hidden');
    });
    
    // Close share modal
    modalClose.addEventListener('click', closeShareModal);
    modalOverlay.addEventListener('click', closeShareModal);
    
    function closeShareModal() {
        shareModal.classList.add('hidden');
    }
}

function copyToClipboard(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    
    navigator.clipboard.writeText(input.value).then(() => {
        // Show feedback
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Copied!';
        button.style.backgroundColor = '#10b981';
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.style.backgroundColor = '';
        }, 2000);
    });
}

// ==========================================
// SCROLL ANIMATIONS
// ==========================================

function initializeScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeIn 0.6s ease forwards';
            }
        });
    }, observerOptions);
    
    // Observe elements
    const elements = document.querySelectorAll('.article-content, .sidebar-category, .empty-state');
    elements.forEach(el => observer.observe(el));
}

// ==========================================
// COPY CODE BUTTONS
// ==========================================

function initializeCopyButtons() {
    const codeBlocks = document.querySelectorAll('pre code');
    
    codeBlocks.forEach((codeBlock) => {
        const pre = codeBlock.parentElement;
        
        // Create copy button
        const copyButton = document.createElement('button');
        copyButton.className = 'copy-code-btn';
        copyButton.innerHTML = '<i class="fas fa-copy"></i>';
        copyButton.style.position = 'absolute';
        copyButton.style.top = '0.5rem';
        copyButton.style.right = '0.5rem';
        copyButton.style.padding = '0.5rem';
        copyButton.style.backgroundColor = 'var(--bg-tertiary)';
        copyButton.style.border = 'none';
        copyButton.style.borderRadius = 'var(--border-radius)';
        copyButton.style.color = 'var(--text-secondary)';
        copyButton.style.cursor = 'pointer';
        copyButton.style.transition = 'all 0.3s ease';
        copyButton.style.fontSize = '0.9rem';
        
        copyButton.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'var(--accent-primary)';
            this.style.color = 'white';
        });
        
        copyButton.addEventListener('mouseleave', function() {
            this.style.backgroundColor = 'var(--bg-tertiary)';
            this.style.color = 'var(--text-secondary)';
        });
        
        copyButton.addEventListener('click', function() {
            const code = codeBlock.textContent;
            navigator.clipboard.writeText(code).then(() => {
                this.innerHTML = '<i class="fas fa-check"></i>';
                this.style.backgroundColor = '#10b981';
                
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-copy"></i>';
                    this.style.backgroundColor = 'var(--bg-tertiary)';
                }, 2000);
            });
        });
        
        pre.style.position = 'relative';
        pre.appendChild(copyButton);
    });
}

// ==========================================
// UTILITY FUNCTIONS
// ==========================================

// Smooth scroll to top
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Check if element is in viewport
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ==========================================
// KEYBOARD SHORTCUTS
// ==========================================

document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K to open search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('searchBtn').click();
    }
    
    // Ctrl/Cmd + / to toggle sidebar (on mobile)
    if ((e.ctrlKey || e.metaKey) && e.key === '/') {
        e.preventDefault();
        const toggle = document.getElementById('sidebarToggle');
        if (toggle && window.innerWidth <= 1024) {
            toggle.click();
        }
    }
});

// ==========================================
// LINK HANDLING
// ==========================================

// Update URL hash on heading click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('heading-anchor') || 
        e.target.parentElement.classList.contains('heading-anchor')) {
        e.preventDefault();
        const anchor = e.target.classList.contains('heading-anchor') ? e.target : e.target.parentElement;
        const hash = anchor.getAttribute('href');
        history.pushState(null, null, hash);
        
        const element = document.querySelector(hash);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }
});

// ==========================================
// PERFORMANCE OPTIMIZATION
// ==========================================

// Lazy load images
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    const lazyImages = document.querySelectorAll('img.lazy');
    lazyImages.forEach(img => imageObserver.observe(img));
}

console.log('Documentation system initialized successfully!');
