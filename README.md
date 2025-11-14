# Modern Documentation System

A beautiful, feature-rich documentation system built with PHP, HTML, CSS (TailwindCSS), and JavaScript. Inspired by GitBook's layout with extensive theming, animations, and modern UI/UX.

## Features

### Client-Facing Documentation
- **GitBook-Inspired Layout**: Collapsible sidebar navigation with category organization
- **8 Beautiful Themes**: Dark, Light, Ocean, Forest, Sunset, Neon, Midnight, and Spring themes
- **Markdown Support**: Write articles in Markdown with syntax highlighting
- **Advanced Search**: Real-time search across all articles with highlighted results
- **Share Functionality**: Share article links or specific section permalinks
- **Table of Contents**: Auto-generated, scrollspy-enabled TOC for easy navigation
- **Responsive Design**: Fully responsive, works beautifully on all devices
- **Modern Animations**: Smooth transitions, fade-ins, and interactive effects
- **Code Highlighting**: Syntax highlighting for multiple programming languages

### Admin Panel
- **Password Protected**: Secure admin access with password authentication
- **Article Management**: Create, edit, and delete articles with a rich Markdown editor
- **Category Management**: Organize articles into categories
- **Dashboard**: Overview with statistics and quick actions
- **Settings Panel**: Configure site name, description, and admin password
- **Real-time Updates**: Instant feedback with notifications

### Technical Features
- **No Database Required**: All data stored in JSON files
- **Clean Architecture**: Organized file structure with separation of concerns
- **Modern PHP**: Object-oriented approach with security best practices
- **Performance Optimized**: Fast loading with lazy loading and efficient rendering
- **SEO Friendly**: Semantic HTML and proper meta tags
- **Accessibility**: ARIA labels and keyboard shortcuts

## Installation

1. **Clone or download** this repository to your web server
2. **Ensure PHP 7.4+** is installed
3. **Set permissions** on the `data` directory to be writable:
   ```bash
   chmod -R 755 data
   ```
4. **Access the site** through your web browser

## Default Credentials

- **Admin URL**: `yourdomain.com/admin/`
- **Default Password**: `password`

**Important**: Change the default password immediately after first login!

## File Structure

```
files/
├── index.php              # Main documentation viewer
├── admin/                 # Admin panel
│   ├── index.php         # Admin dashboard
│   ├── login.php         # Admin login
│   ├── api.php           # API endpoints
│   ├── admin.css         # Admin styles
│   └── admin.js          # Admin JavaScript
├── assets/               # Static assets
│   ├── css/
│   │   └── style.css     # Main stylesheet with themes
│   └── js/
│       └── main.js       # Main JavaScript
├── data/                 # JSON data storage
│   ├── config.json       # Site configuration
│   ├── categories.json   # Categories data
│   └── articles.json     # Articles data
└── includes/
    └── functions.php     # Helper functions
```

## Usage

### Creating Articles

1. Log in to the admin panel
2. Go to "Articles" tab
3. Click "New Article"
4. Enter title, select category, and write content in Markdown
5. Click "Save Article"

### Creating Categories

1. Go to "Categories" tab in admin
2. Click "New Category"
3. Enter name and optional description
4. Click "Save Category"

### Markdown Guide

The system supports full Markdown syntax:

```markdown
# Heading 1
## Heading 2
### Heading 3

**Bold text**
*Italic text*

- Bullet list
1. Numbered list

[Link](https://example.com)
![Image](image-url.jpg)

`inline code`

```javascript
// Code block
console.log('Hello World');
```

> Blockquote
```

### Keyboard Shortcuts

- **Ctrl/Cmd + K**: Open search
- **Ctrl/Cmd + /**: Toggle sidebar (mobile)
- **ESC**: Close modals

## Themes

The system includes 8 carefully crafted themes:

1. **Dark** - Classic dark theme (default)
2. **Light** - Clean light theme
3. **Ocean** - Deep blue ocean-inspired
4. **Forest** - Natural green tones
5. **Sunset** - Warm sunset colors
6. **Neon** - Vibrant neon cyberpunk
7. **Midnight** - Deep indigo night theme
8. **Spring** - Soft pastel spring colors

Users can switch themes using the palette icon in the header. Theme preference is saved in browser.

## Security

- Password-protected admin panel with bcrypt hashing
- Session-based authentication
- Input sanitization to prevent XSS attacks
- CSRF protection recommended for production

## Customization

### Changing Colors

Edit theme variables in `assets/css/style.css`:

```css
[data-theme="yourtheme"] {
    --bg-primary: #your-color;
    --accent-primary: #your-color;
    /* ... more variables */
}
```

### Adding New Features

The modular architecture makes it easy to extend:
- Add new API endpoints in `admin/api.php`
- Add new UI components in respective files
- Extend functionality in `includes/functions.php`

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance

- Optimized animations using CSS transforms
- Lazy loading for images
- Efficient DOM manipulation
- Minimal external dependencies

## License

MIT License - See LICENSE file for details

## Credits

Built with:
- [TailwindCSS](https://tailwindcss.com/) - Utility-first CSS framework
- [Font Awesome](https://fontawesome.com/) - Icon library
- [Marked.js](https://marked.js.org/) - Markdown parser
- [Prism.js](https://prismjs.com/) - Syntax highlighter

## Support

For issues or questions, please open an issue on the repository.

## Changelog

### Version 1.0.0 (2025-11-14)
- Initial release
- Full documentation system
- Admin panel
- 8 themes
- Markdown support
- Search functionality
- Share feature
