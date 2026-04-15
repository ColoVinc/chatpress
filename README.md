# 🤖 SiteGenie — AI Assistant for WordPress

SiteGenie integrates artificial intelligence directly into the WordPress admin panel. It's not just a chatbot: it can perform **real actions** on your site through agentic function calling.

![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-blue?logo=wordpress)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php)
![License](https://img.shields.io/badge/License-GPL--2.0-green)
![Version](https://img.shields.io/badge/Version-0.1.0-orange)

---

## ✨ Features

- **AI Chat in the back-office** — Floating widget available on every admin page with conversation history
- **Content generation** — Metabox in the post editor to generate article drafts and SEO meta (title, description, excerpt)
- **Agentic function calling** — The AI can create, edit and delete posts, pages and Custom Post Types autonomously
- **ACF support** — Automatically discovers and fills Advanced Custom Fields with type-based validation
- **Multi-provider** — Supports Google Gemini, OpenAI (GPT) and Anthropic Claude
- **Site context** — Configure name, sector, tone and target audience for content consistent with your brand
- **API call logs** — Monitor tokens consumed, errors and full API call history with pagination
- **Rate limiting** — Configurable per-user hourly request limit
- **Auto-cleanup** — Daily cron job to automatically delete old conversations
- **Privacy** — Auto-registers a privacy policy suggestion in WordPress

## 🧠 Available AI Tools

The AI assistant can execute these actions on your WordPress site:

| Tool | Description |
|------|-------------|
| `create_post` | Create a new post or page |
| `update_post` | Edit an existing post or page |
| `delete_post` | Move a post to trash |
| `get_posts` | Retrieve and search posts/pages |
| `get_media` | Browse the media library |
| `get_categories` | List all categories |
| `get_site_info` | Get site name, URL, theme, plugins, stats |
| `get_custom_post_types` | Discover all CPTs with their ACF fields |
| `create_custom_post` | Create a CPT entry and populate ACF fields |
| `update_custom_post` | Update a CPT entry and its ACF fields |

## 🔌 Supported Providers

| Provider | Models | Free Tier |
|----------|--------|-----------|
| **Google Gemini** | Gemini 2.5 Flash-Lite, Flash, Pro | Up to 1,000 req/day (Flash-Lite) |
| **OpenAI** | GPT-4o Mini, GPT-4o, GPT-4.1 | No free tier |
| **Anthropic Claude** | Claude Sonnet 4, Claude Haiku 4 | No free tier |

Get your API keys:
- **Gemini** → [Google AI Studio](https://aistudio.google.com)
- **OpenAI** → [OpenAI Platform](https://platform.openai.com/api-keys)
- **Claude** → [Anthropic Console](https://console.anthropic.com/settings/keys)

## 📦 Installation

### 1. Download and install

Clone or download this repository into your WordPress plugins directory:

```bash
cd wp-content/plugins/
git clone https://github.com/vincenzocolonna/sitegenie.git
```

### 2. Install vendor dependencies

This repository does not include third-party CSS/JS libraries. You need to download them and place them in `assets/vendor/`:

**Bootstrap 5.3.3:**
- Download from [getbootstrap.com](https://getbootstrap.com/docs/5.3/getting-started/download/)
- Place `bootstrap.min.css` in `assets/vendor/`
- Place `bootstrap.bundle.min.js` in `assets/vendor/`

**Font Awesome 6.5.1 (Free):**
- Download from [fontawesome.com](https://fontawesome.com/download)
- Place `fontawesome.min.css` (the `all.min.css` renamed) in `assets/vendor/`
- Place the `webfonts/` folder in `assets/vendor/webfonts/` (you need at least `fa-solid-900.woff2`, `fa-solid-900.ttf`, `fa-regular-400.woff2`, `fa-regular-400.ttf`, `fa-brands-400.woff2`, `fa-brands-400.ttf`)

Your `assets/vendor/` folder should look like this:

```
assets/vendor/
├── bootstrap.min.css
├── bootstrap.bundle.min.js
├── fontawesome.min.css
└── webfonts/
    ├── fa-solid-900.woff2
    ├── fa-solid-900.ttf
    ├── fa-regular-400.woff2
    ├── fa-regular-400.ttf
    ├── fa-brands-400.woff2
    └── fa-brands-400.ttf
```

### 3. Activate and configure

1. Activate the plugin from **Plugins** in WordPress admin
2. Go to **SiteGenie → Settings**
3. Select your AI provider and enter the API key
4. Optionally configure the site context (name, sector, tone, target audience)
5. Click the 🤖 robot icon in the bottom-right corner to start chatting

## 🏗️ Project Structure

```
sitegenie/
├── sitegenie.php                  # Main plugin file, bootstrap, DB tables
├── includes/
│   ├── class-core.php             # Core singleton, hooks initialization
│   ├── class-api-connector.php    # Abstract base class for AI providers
│   ├── class-tools.php            # Tool declarations and execution engine
│   ├── class-history.php          # Conversation and message CRUD
│   ├── class-logger.php           # API call logging and statistics
│   └── connectors/
│       ├── class-gemini.php       # Google Gemini connector
│       ├── class-openai.php       # OpenAI GPT connector
│       └── class-claude.php       # Anthropic Claude connector
├── admin/
│   ├── class-admin.php            # Settings page, menu, connector factory
│   ├── class-chat.php             # Chat widget and AJAX endpoints
│   └── class-metabox.php          # Editor metabox for content/SEO generation
├── templates/
│   ├── settings-page.php          # Settings page template
│   ├── chat-widget.php            # Chat widget HTML
│   ├── metabox.php                # Metabox HTML
│   └── logs-page.php              # Logs page with pagination
└── assets/
    ├── css/
    │   ├── admin.css              # Admin pages styles
    │   └── chat.css               # Chat widget and metabox styles
    └── js/
        ├── admin.js               # Settings page logic
        ├── chat.js                # Chat widget logic
        └── metabox.js             # Metabox logic (Gutenberg + Classic Editor)
```

## 🗄️ Database

The plugin creates 3 custom tables on activation:

| Table | Purpose |
|-------|---------|
| `wp_sitegenie_conversations` | Chat conversations per user |
| `wp_sitegenie_messages` | Individual messages (role: user/model) |
| `wp_sitegenie_logs` | API call logs with token counts |

All tables are automatically removed when the plugin is deleted (not deactivated).

## 🔒 Security

- Nonce verification on all AJAX endpoints
- Capability checks (`manage_options` for admin settings, `edit_posts` for chat and metabox)
- Input sanitization on all user data
- Conversation ownership verification (users can only access their own conversations)
- Configurable per-user hourly rate limiting
- API keys stored in `wp_options`, never hardcoded

## 📋 Requirements

- WordPress 5.8+
- PHP 7.4+
- An API key from at least one supported provider

## 📄 License

This project is licensed under the [GPL-2.0+](https://www.gnu.org/licenses/gpl-2.0.html) license.

## 👤 Author

**Vincenzo Colonna** — [GitHub](https://github.com/vincenzocolonna)
