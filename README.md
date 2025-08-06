# Bonza Quote Management Plugin

A custom WordPress plugin for managing incoming service quote requests. Built as part of a task to simulate the Bonza workflow for handling client inquiries and admin approvals.

---

## 🧩 Features

### ✅ Frontend Quote Form
- Embed with `[bonza_quote_form]` shortcode
- Fields: **Name**, **Email**, **Service Type**, and **Notes**
- Submissions are saved with a `pending` status
- Success message shown after submission

### 🔐 Admin Quote Dashboard
- Accessible via **Bonza Quotes** in the WordPress Admin menu
- Displays submitted quotes in a table format
- Allows status change to **Approved** or **Rejected**

### 🔧 Technical Highlights
- OOP (Object-Oriented Programming) structure
- Clean, modular codebase following WordPress best practices
- Full sanitization, escaping, and security implementation

---

## 🎁 Bonus Features
- Email notification sent to admin when a new quote is submitted
- WordPress action hooks included for extensibility

---

## 🧪 Testing

This plugin includes a basic PHPUnit test located in the `tests` directory.

### 📦 Prerequisites
- WordPress installed locally or in a test environment
- [WP-CLI](https://wp-cli.org/) (optional but helpful)
- PHP 7.4 or higher
- Composer

### 🚀 Running Tests

1. Install dependencies:
   ```bash
   composer install
2. Set up test suite (if needed):
   ```bash
      wp scaffold plugin-tests bonza-quote-manager
3. Run PHPUnit:
   ```bash
      ./vendor/bin/phpunit

**Note:** See `phpunit.xml.dist` for test configuration.

### ⚙️ Installation Instructions

1. Clone the repo into your /wp-content/plugins directory:
   ```bash
      git clone https://github.com/your-username/bonza-quote-plugin.git
2. Activate the plugin in the WordPress Admin → Plugins.
3. Add the shortcode `[bonza_quote_form]` to any post or page.

### 📌 Assumptions

- The plugin uses a custom post type called bonza_quote to store submissions.
- Email notifications are sent to the WordPress admin_email.
- No frontend styling is included — it inherits from your theme.

### 🧠 Extensibility

The plugin includes the following WordPress hooks:
- **bonza_quote_submitted** — Triggered after a quote is saved
- **bonza_quote_status_updated** — Triggered after a status change
Developers can hook into these to extend functionality.

### 📝 License

This plugin is licensed under the GNU General Public License v2.0 or later.
See https://www.gnu.org/licenses/gpl-2.0.html for full terms.

### 📬 Contact

Developed by Eduardo Bautista
Questions? Reach me via GitHub or eduardobautista2089@gmail.com