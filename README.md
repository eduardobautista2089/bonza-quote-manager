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
